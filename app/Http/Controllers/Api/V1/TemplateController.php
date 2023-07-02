<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Template;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class TemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $template = Template::latest()->paginate(5);

        return response()->json($template);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $formFields = $request->validate([
            'description' => 'required',
            'picture' => 'required',
        ]);

        if ($request->hasFile('picture')) {
            $formFields['picture'] = $request->file('picture')->store('picture', 'public');
        }

        $formFields['user_id'] = auth()->id();

        $template = Template::create($formFields);

        return response()->json([
            'status' => 'success',
            'message' => 'successfully stored',
            'data' => $template
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Template $template)
    {
        if ($template->user_id != auth()->id()) {
            abort(403, 'Unauthorized Action');
        }

        $formFields = $request->validate([
            'description' => 'required',
            'picture' => 'required',
        ]);

        if ($request->hasFile('picture')) {
            $oldPicturePath = $template->picture;
            $newPicturePath = $request->file('picture')->store('picture', 'public');

            $formFields['picture'] = $newPicturePath;

            // Delete old picture
            if ($oldPicturePath) {
                Storage::delete($oldPicturePath);
            }
        }

        $template->update($formFields);

        return response()->json([
            'status' => 'success',
            'message' => 'updated successfully',
            'data' => $template
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Template $template)
    {
        $template->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'deleted successfully',
            'data' => $template
        ]);
    }
}
