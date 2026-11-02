<?php

namespace App\Services;

use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use Illuminate\Support\Facades\Request;
use App\Models\AdminCountry;

class ProjectService
{
    public function create($data, $userId = false): void
    {
        $data['user_id'] = $userId ? $userId : auth()->user()->id;
        Project::create($data);
    }


    public static function getById($id)
    {
        return Project::withTrashed()->find($id);
    }

    public static function getAdminProjects($searchQuery = '')
    {
        $query = Project::withTrashed()->withProjectInfo()->authorizedProjects();

        if ($searchQuery !== '') {
            $query = $query->where('title', 'like', '%' . $searchQuery . '%');
        }

        return $query->get();
    }

    public function update($id, $request)
    {
    }

    public function delete($id)
    {
        $project = Project::find($id);
        $project->delete();
    }
}
