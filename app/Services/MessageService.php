<?php

namespace App\Services;

use App\Models\Audition;
use App\Models\AuditionApplication;
use App\Http\Resources\AuditionMessageResource;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Models\Profile;
use App\Models\ProfileMessage;

class MessageService
{
    public function queryUserApplicationMessages()
    {
        $profileId = auth()->user()->profile->id;

        return Audition::whereHas('applications', function ($query) use ($profileId) {
            $query->where('user_profile_id', $profileId);
        })
            ->with([
                'applications' => function ($query) use ($profileId) {
                    $query->where('user_profile_id', $profileId);
                },
                'messages' => function ($query) use ($profileId) {
                    $query->with(['profileMessage' => function ($q) use ($profileId) {
                        $q->where('user_profile_id', $profileId);
                    }, 'options']);
                }
            ])
            ->get()
            ->map(function ($audition) {
                $application = $audition->applications->first();

                $messages = $audition->messages->filter(function ($message) use ($application) {
                    if ($message->for_accepted && $application->is_accepted) {
                        return true;
                    }

                    if ($message->for_shortlisted && $application->is_shortlisted) {
                        return true;
                    }

                    if ($message->for_applicants && !$application->is_accepted && !$application->is_shortlisted) {
                        return true;
                    }

                    if ($message->profileMessage->isNotEmpty()) {
                        return true;
                    }
                    return false;
                });

                return [
                    'audition' => $audition,
                    'messages' => $messages
                ];
            });
    }

    public function hasUnreadAuditionMessages() {
        $_auditions = $this->queryUserApplicationMessages();
        $unreadCount = 0;

        $_auditions->each(function ($audition) use (&$unreadCount) {
            $unreadCount += $audition['messages']->filter(function ($message) {
                return $message->profileMessage->isEmpty() || $message->profileMessage->first()->read == 0;
            })->count();
        });

        return $unreadCount > 0;
    }

    public function getUserApplicationsMessages()
    {
        $_auditions = $this->queryUserApplicationMessages();
          
        $auditions = $_auditions->map(function ($audition) {
            $unreadCount = 0;

            $audition['message'] = collect($audition['messages'])
                ->map(function ($message) use (&$unreadCount) {
                    if ($message->profileMessage->isEmpty() || $message->profileMessage->first()->read == 0) {
                        $unreadCount++;
                    }
                    return $message;
                });

            $audition['unread'] = $unreadCount;

            $audition['last_message_at'] = sizeof($audition['message']->toArray()) > 0 ? $audition['message']->last()->created_at : null;

            return $audition;
        })
            ->sortByDesc('last_message_at');

        return AuditionMessageResource::collection($auditions);
    }

    public function getAuditionChat($auditionId)
    {
        $profileId = auth()->user()->profile->id;

        $audition = Audition::whereHas('applications', function ($query) use ($profileId) {
            $query->where('user_profile_id', $profileId);
        })
            ->with([
                'applications' => function ($query) use ($profileId) {
                    $query->where('user_profile_id', $profileId);
                },
                'messages' => function ($query) use ($profileId) {
                    $query->with(['profileMessage' => function ($q) use ($profileId) {
                        $q->where('user_profile_id', $profileId);
                    }, 'options']);
                }
            ])
            ->find($auditionId);


        $application = $audition->applications->first();

        $messages = $audition->messages->filter(function ($message) use ($application) {
            if ($message->for_accepted && $application->is_accepted) {
                return true;
            }

            if ($message->for_shortlisted && $application->is_shortlisted) {
                return true;
            }

            if ($message->for_applicants && !$application->is_accepted && !$application->is_shortlisted) {
                return true;
            }

            if ($message->profileMessage->isNotEmpty()) {
                return true;
            }
            return false;
        });

        return MessageResource::collection($messages);
    }

    public function answer($auditionMessage, $option_id)
    {
        ProfileMessage::updateOrCreate([
            'user_profile_id' => auth()->user()->profile->id,
            'audition_message_id' => $auditionMessage->id
        ], ['audition_messages_option_id' => $option_id, 'read_by_admin' => 0]);
    }

    public function markRead($auditionId)
    {

        $profileId = auth()->user()->profile->id;

        $audition = Audition::whereHas('applications', function ($query) use ($profileId) {
            $query->where('user_profile_id', $profileId);
        })
            ->with([
                'applications' => function ($query) use ($profileId) {
                    $query->where('user_profile_id', $profileId);
                },
                'messages' => function ($query) use ($profileId) {
                    $query->with(['profileMessage' => function ($q) use ($profileId) {
                        $q->where('user_profile_id', $profileId);
                    }, 'options']);
                }
            ])
            ->find($auditionId);


        $application = $audition->applications->first();

        $messages = $audition->messages->filter(function ($message) use ($application) {
            if ($message->for_accepted && $application->is_accepted) {
                return true;
            }

            if ($message->for_shortlisted && $application->is_shortlisted) {
                return true;
            }

            if ($message->for_applicants && !$application->is_accepted && !$application->is_shortlisted) {
                return true;
            }

            if ($message->profileMessage->isNotEmpty()) {
                return true;
            }
            return false;
        })->pluck('id');


        $existing = ProfileMessage::whereIn('audition_message_id', $messages)
            ->where('user_profile_id', $profileId)
            ->get();

        $toCreate = $messages->diff($existing->pluck('audition_message_id'));

        ProfileMessage::insert($toCreate->map(function ($message) use ($profileId) {
            return [
                'audition_message_id' => $message,
                'user_profile_id' => $profileId,
                'read' => 1
            ];
        })->toArray());

        ProfileMessage::whereIn('audition_message_id', $messages)
            ->where('user_profile_id', $profileId)
            ->update(['read' => 1]);
    }

    /**
     * When a user applies to an audition, or has change in his/her application status, the messages that were intended for his application status group will be assigned to this user
     * @param string $applicationStatusField for_applicants | for_shortlisted | for_accepted
     */
    public static function syncUserMessages($profileId, $auditionId, array $statusCondition)
    {

        $previousMessages =  Message::where([
            'audition_id' => $auditionId,
            ...$statusCondition
        ])
            ->whereHas('profileMessage', function ($query) use ($profileId) {
                $query->where('user_profile_id', '!=', $profileId);
            })
            ->get();

        if ($previousMessages->count() > 0) {
            Profile::find($profileId)->messages()->attach($previousMessages);
        }
    }

    public static function getAdminAuditionChats($auditionId)
    {
        $audition = Audition::with([
            'applications.profile.messages' => function ($query) use ($auditionId) {
                $query->where('audition_id', $auditionId);
            },
            'applications.profile.messages.options',
            'applications.profile.castingImages'
        ])->find($auditionId);

        $audition->applications->map(function ($application) {
            $unreadCount = 0;

            $application->profile->messages->map(function ($message) use (&$unreadCount) {
                if (!$message->pivot->read_by_admin) {
                    $unreadCount++;
                }
            });

            $application->unread_by_admin = $unreadCount;

            return $application;
        });

        return $audition;
    }

    public static function getAdminAuditionApplicationChat($auditionId, $applicationId)
    {
        $application =  AuditionApplication::with([
            'profile.messages' => function ($query) use ($auditionId,) {
                $query->where('audition_id', $auditionId);
            },
            'profile.messages.options',
            'profile.castingImages'
        ])->find($applicationId);

        $unreadCount = 0;

        $application->profile->messages->map(function ($message) use (&$unreadCount) {
            if (!$message->pivot->read_by_admin) {
                $unreadCount++;
            }
        });

        $application->unread_by_admin = $unreadCount;

        return $application;
    }

    public static function markReadByAdmin(AuditionApplication $application)
    {
        $application->profile->messages->each(function ($message) {
            $message->pivot->update(['read_by_admin' => 1], ['timestamps' => false]);
        });
    }

    /**
     * Summary of create
     * @param array {
     * message: string,
     * audition_id: int | string
     * for_applicants?: boolean
     * for_shortlisted?: boolean
     * for_accepted?: boolean
     * recipients?: array of profile ids
     * }
     * 
     * @return Message|\Illuminate\Database\Eloquent\Model
     */
    public static function create($data)
    {
        $message = Message::create([
            'message' => $data['message'],
            'audition_id' => $data['audition_id'],
            'for_applicants' => $data['for_applicants'] ?? false,
            'for_shortlisted' => $data['for_shortlisted'] ?? false,
            'for_accepted' => $data['for_accepted'] ?? false,
        ]);

        // this means applicants are selected manually
        if (self::recipientsSelectedManually($data)) {
            $message->recipients()->attach($data['recipients']);
        }

        if (isset($data['answer_options']) && sizeof($data['answer_options']) > 0) {
            foreach ($data['answer_options'] as $option) {
                $message->options()->create(['option' => $option]);
            }
        }

        return $message;
    }

    private static function recipientsSelectedManually($data)
    {
        return isset($data['recipients']) && sizeof($data['recipients']) > 0  && $data['for_applicants'] == false && $data['for_shortlisted'] == false && $data['for_accepted'] == false;
    }
}
