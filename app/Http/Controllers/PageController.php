<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Event;
use App\Models\Page;
use App\Models\User;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display the specified page.
     */
    public function show($slug)
    {
        $page = Page::where('slug', $slug)
            ->published()
            ->firstOrFail();

        return view('pages.show', compact('page'));
    }

    /**
     * Display the homepage.
     */
    public function home()
    {
        $page = Page::where('slug', 'home')
            ->published()
            ->firstOrFail();

        // Get upcoming events for homepage
        $upcomingEvents = Event::upcoming()
            ->take(3)
            ->get();

        $featuredCourses = Course::published()
            ->with('instructor')
            ->orderByDesc('is_featured')
            ->orderByDesc('enrollment_count')
            ->take(3)
            ->get();

        $recentPosts = BlogPost::published()
            ->with('category')
            ->recent()
            ->take(3)
            ->get();

        $impactStats = [
            'active_learners' => Enrollment::active()->distinct('user_id')->count('user_id'),
            'upcoming_events' => Event::upcoming()->count(),
            'community_members' => User::where('is_admin', false)->count(),
        ];

        return view('pages.home', compact(
            'page',
            'upcomingEvents',
            'featuredCourses',
            'recentPosts',
            'impactStats'
        ));
    }

    /**
     * Display the about page.
     */
    public function about()
    {
        $page = Page::where('slug', 'about')
            ->published()
            ->firstOrFail();

        $aboutStats = [
            'active_learners' => Enrollment::active()->distinct('user_id')->count('user_id'),
            'upcoming_events' => Event::upcoming()->count(),
            'community_members' => User::where('is_admin', false)->count(),
        ];

        return view('pages.about', compact('page', 'aboutStats'));
    }

    /**
     * Display the contact page.
     */
    public function contact(Request $request)
    {
        $page = Page::where('slug', 'contact')
            ->published()
            ->firstOrFail();

        if (
            $request->boolean('refresh_captcha')
            || ! $request->session()->has('contact_captcha_question')
            || ! $request->session()->has('contact_captcha_answer')
        ) {
            $this->regenerateMathCaptcha($request);
        }

        $captchaQuestion = $request->session()->get('contact_captcha_question');

        return view('pages.contact', compact('page', 'captchaQuestion'));
    }

    /**
     * Display the programs page.
     */
    public function programs()
    {
        $page = Page::where('slug', 'programs')
            ->published()
            ->firstOrFail();

        $featuredCourses = Course::published()
            ->with('instructor')
            ->orderByDesc('is_featured')
            ->orderByDesc('enrollment_count')
            ->take(3)
            ->get();

        $upcomingEvents = Event::upcoming()
            ->take(3)
            ->get();

        return view('pages.programs', compact('page', 'featuredCourses', 'upcomingEvents'));
    }

    /**
     * Display the donations page.
     */
    public function donations()
    {
        $page = Page::where('slug', 'donations')
            ->published()
            ->firstOrFail();

        return view('pages.donations', compact('page'));
    }

    /**
     * Display the accessibility page.
     */
    public function accessibility()
    {
        $page = Page::where('slug', 'accessibility')
            ->published()
            ->firstOrFail();

        return view('pages.accessibility', compact('page'));
    }

    private function regenerateMathCaptcha(Request $request): void
    {
        $left = random_int(1, 9);
        $right = random_int(1, 9);

        $request->session()->put('contact_captcha_question', "{$left} + {$right}");
        $request->session()->put('contact_captcha_answer', $left + $right);
    }
}
