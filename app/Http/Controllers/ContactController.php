<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(){
        return view('pages.contact');
    }

    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:500',
        ]);

        // Simulate storing the contact message
        // In a real application, you would save this to the database
        // For now, we'll just return a success message
        return redirect()->route('contact')->with('success', 'Your message has been sent successfully!');
    }
}
