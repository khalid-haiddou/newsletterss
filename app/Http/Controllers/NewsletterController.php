<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use App\Models\Newsletter;
use App\Models\User;
use Illuminate\Http\Request;
use App\Jobs\SendMailJob;

class NewsletterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $newsletters = Newsletter::all();
        $users = User::all();
        $categories = Category::all(); 
        return view('news', [
            'newsletters' => $newsletters,
            'users' => $users,
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'user_id' => 'required|exists:users,id',
            'images' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'categories' => 'array',
        ]);

        // Handle image upload
        $imageName = null;
        if ($request->hasFile('images')) {
            $imageName = $request->file('images')->getClientOriginalName();
            $request->file('images')->move(public_path('assets/images'), $imageName);
        }

        $newsletter = Newsletter::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => $request->user_id,
            'images' => $imageName,
        ]);

        if ($request->has('categories')) {
            $newsletter->categories()->attach($request->categories);
        }

        return redirect('newsletter')->with('success', 'Newsletter created successfully.');
    }


    public function update(Request $request, Newsletter $newsletter)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'user_id' => 'required|exists:users,id',
            'images' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $newsletter->title = $request->title;
        $newsletter->content = $request->content;
        $newsletter->user_id = $request->user_id;

        if ($request->hasFile('images')) {
            $imageName = $request->file('images')->getClientOriginalName();
            $request->file('images')->move(public_path('assets/images'), $imageName);
            $newsletter->images = $imageName;
        }

        $newsletter->save();

        return redirect('newsletter')->with('success', 'Newsletter updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Newsletter $newsletter)
    {
        if ($newsletter->image) {
            $imagePath = 'issets/images/' . $newsletter->image;
            if (Storage::exists($imagePath)) {
                Storage::delete($imagePath);
            }
        }
        $newsletter->delete();
        return redirect('newsletter')
            ->with('success', 'Newsletter deleted successfully.');
    }
}
