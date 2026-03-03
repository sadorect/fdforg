@extends('layouts.admin')

@section('title', 'Admin & LMS Manual')

@section('content')
<div class="space-y-6">
    <section class="rounded-lg bg-white p-6 shadow">
        <h1 class="text-3xl font-bold text-gray-900">Admin & LMS User Manual</h1>
        <p class="mt-2 text-gray-600">
            This guide is for Admins and Delegated Admins managing the Friends of The Deaf Foundation website and LMS.
            Follow the sections in order if you are new, or jump directly to the task you need.
        </p>
        <div class="mt-4 flex flex-wrap gap-2">
            <a href="#quick-start" class="rounded bg-blue-100 px-3 py-1 text-sm font-medium text-blue-700">Quick Start</a>
            <a href="#content-admin" class="rounded bg-indigo-100 px-3 py-1 text-sm font-medium text-indigo-700">Content Administration</a>
            <a href="#lms-admin" class="rounded bg-emerald-100 px-3 py-1 text-sm font-medium text-emerald-700">LMS Administration</a>
            <a href="#delegated-admins" class="rounded bg-amber-100 px-3 py-1 text-sm font-medium text-amber-700">Delegated Admins</a>
            <a href="#operations" class="rounded bg-purple-100 px-3 py-1 text-sm font-medium text-purple-700">Operations Checklist</a>
            <a href="#troubleshooting" class="rounded bg-rose-100 px-3 py-1 text-sm font-medium text-rose-700">Troubleshooting</a>
        </div>
    </section>

    <section id="quick-start" class="rounded-lg bg-white p-6 shadow">
        <h2 class="text-2xl font-semibold text-gray-900">1. Quick Start (First 15 Minutes)</h2>
        <ol class="mt-4 list-decimal space-y-2 pl-5 text-gray-700">
            <li>Open <a class="font-medium text-blue-600 hover:text-blue-800" href="{{ route('admin.dashboard') }}">Dashboard</a> to review site and LMS activity.</li>
            <li>Open <a class="font-medium text-blue-600 hover:text-blue-800" href="{{ route('admin.analytics') }}">Analytics</a> to check traffic trends and popular pages.</li>
            <li>For website updates, use <a class="font-medium text-blue-600 hover:text-blue-800" href="{{ route('admin.pages') }}">Pages</a>, <a class="font-medium text-blue-600 hover:text-blue-800" href="{{ route('admin.events') }}">Events</a>, and <a class="font-medium text-blue-600 hover:text-blue-800" href="{{ route('admin.blog') }}">Blog</a>.</li>
            <li>For learning updates, use <a class="font-medium text-blue-600 hover:text-blue-800" href="{{ route('admin.courses') }}">Courses</a>, <a class="font-medium text-blue-600 hover:text-blue-800" href="{{ route('admin.lessons') }}">Lessons</a>, and <a class="font-medium text-blue-600 hover:text-blue-800" href="{{ route('admin.enrollments') }}">Enrollments</a>.</li>
            <li>When done, verify as a public user by opening the homepage and course pages in a separate browser tab.</li>
        </ol>
    </section>

    <section id="content-admin" class="rounded-lg bg-white p-6 shadow">
        <h2 class="text-2xl font-semibold text-gray-900">2. Content Administration (Website)</h2>
        <p class="mt-2 text-gray-600">Use this section for pages, events, blog content, and global presentation settings.</p>

        <div class="mt-5 space-y-5">
            <div class="rounded-lg border border-gray-200 p-4">
                <h3 class="text-lg font-semibold text-gray-900">2.1 Manage Pages</h3>
                <p class="mt-2 text-sm text-gray-600">Tool: <a class="font-medium text-blue-600 hover:text-blue-800" href="{{ route('admin.pages') }}">Pages</a></p>
                <ol class="mt-3 list-decimal space-y-2 pl-5 text-sm text-gray-700">
                    <li>Click Create or Edit on the target page.</li>
                    <li>Set a clear title and unique slug (URL path).</li>
                    <li>Use concise headings and short paragraphs for accessibility and readability.</li>
                    <li>Save, then open the public page to confirm layout and links.</li>
                </ol>
            </div>

            <div class="rounded-lg border border-gray-200 p-4">
                <h3 class="text-lg font-semibold text-gray-900">2.2 Manage Events</h3>
                <p class="mt-2 text-sm text-gray-600">Tool: <a class="font-medium text-blue-600 hover:text-blue-800" href="{{ route('admin.events') }}">Events</a></p>
                <ol class="mt-3 list-decimal space-y-2 pl-5 text-sm text-gray-700">
                    <li>Create event title, slug, start/end date, venue, and summary.</li>
                    <li>Enable registration when attendees should sign up online.</li>
                    <li>Publish only after date/time and location are confirmed.</li>
                    <li>After publishing, test the registration flow from the public page.</li>
                </ol>
            </div>

            <div class="rounded-lg border border-gray-200 p-4">
                <h3 class="text-lg font-semibold text-gray-900">2.3 Manage Blog Posts and Categories</h3>
                <p class="mt-2 text-sm text-gray-600">Tools: <a class="font-medium text-blue-600 hover:text-blue-800" href="{{ route('admin.blog') }}">Blog</a> and <a class="font-medium text-blue-600 hover:text-blue-800" href="{{ route('admin.categories') }}">Categories</a></p>
                <ol class="mt-3 list-decimal space-y-2 pl-5 text-sm text-gray-700">
                    <li>Create categories first so posts can be organized correctly.</li>
                    <li>For each post, set title, slug, summary, body, category, and publish date.</li>
                    <li>Keep one clear call to action per post (register, donate, learn more).</li>
                    <li>Preview the post and confirm date/status before publishing.</li>
                </ol>
            </div>

            <div class="rounded-lg border border-gray-200 p-4">
                <h3 class="text-lg font-semibold text-gray-900">2.4 Homepage and Brand Settings</h3>
                <p class="mt-2 text-sm text-gray-600">Tools: <a class="font-medium text-blue-600 hover:text-blue-800" href="{{ route('admin.hero-slides') }}">Hero Slides</a>, <a class="font-medium text-blue-600 hover:text-blue-800" href="{{ route('admin.site-settings') }}">Footer & Branding</a>, <a class="font-medium text-blue-600 hover:text-blue-800" href="{{ route('admin.email-templates') }}">Email Templates</a></p>
                <ol class="mt-3 list-decimal space-y-2 pl-5 text-sm text-gray-700">
                    <li>Keep hero slides short, visual, and action-focused.</li>
                    <li>Update footer details whenever contact or social links change.</li>
                    <li>Use email templates for consistent tone in enrollment and system emails.</li>
                    <li>After updates, send a test email and verify branding on mobile and desktop.</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="lms-admin" class="rounded-lg bg-white p-6 shadow">
        <h2 class="text-2xl font-semibold text-gray-900">3. LMS Administration</h2>
        <p class="mt-2 text-gray-600">Use this section for courses, lessons, learner enrollments, and completion progress quality checks.</p>

        <div class="mt-5 space-y-5">
            <div class="rounded-lg border border-gray-200 p-4">
                <h3 class="text-lg font-semibold text-gray-900">3.1 LMS Dashboard Review</h3>
                <p class="mt-2 text-sm text-gray-600">Tool: <a class="font-medium text-blue-600 hover:text-blue-800" href="{{ route('admin.lms') }}">LMS Dashboard</a></p>
                <ol class="mt-3 list-decimal space-y-2 pl-5 text-sm text-gray-700">
                    <li>Check total learners, active enrollments, and completion trends.</li>
                    <li>Identify courses with high enrollment but low lesson completion.</li>
                    <li>Use findings to prioritize lesson updates or learner support messages.</li>
                </ol>
            </div>

            <div class="rounded-lg border border-gray-200 p-4">
                <h3 class="text-lg font-semibold text-gray-900">3.2 Create and Publish Courses</h3>
                <p class="mt-2 text-sm text-gray-600">Tool: <a class="font-medium text-blue-600 hover:text-blue-800" href="{{ route('admin.courses') }}">Courses</a></p>
                <ol class="mt-3 list-decimal space-y-2 pl-5 text-sm text-gray-700">
                    <li>Create the course with title, category, instructor, price, and status.</li>
                    <li>Use Draft status until all lessons are ready and reviewed.</li>
                    <li>Switch to Published only after testing enrollment and first-lesson access.</li>
                    <li>Confirm currency and pricing are correct before announcement.</li>
                </ol>
            </div>

            <div class="rounded-lg border border-gray-200 p-4">
                <h3 class="text-lg font-semibold text-gray-900">3.3 Build Lessons</h3>
                <p class="mt-2 text-sm text-gray-600">Tool: <a class="font-medium text-blue-600 hover:text-blue-800" href="{{ route('admin.lessons') }}">Lessons</a></p>
                <ol class="mt-3 list-decimal space-y-2 pl-5 text-sm text-gray-700">
                    <li>Add lessons in the intended learning sequence.</li>
                    <li>Use clear objectives at the top of each lesson.</li>
                    <li>Verify lesson content works across desktop and mobile layouts.</li>
                    <li>Test completion tracking with a learner account before publishing major updates.</li>
                </ol>
            </div>

            <div class="rounded-lg border border-gray-200 p-4">
                <h3 class="text-lg font-semibold text-gray-900">3.4 Manage Enrollments and Payments</h3>
                <p class="mt-2 text-sm text-gray-600">Tool: <a class="font-medium text-blue-600 hover:text-blue-800" href="{{ route('admin.enrollments') }}">Enrollments</a></p>
                <ol class="mt-3 list-decimal space-y-2 pl-5 text-sm text-gray-700">
                    <li>Review pending or failed enrollment/payment records daily.</li>
                    <li>Confirm each enrollment is tied to the correct learner and course.</li>
                    <li>Resolve stuck or incorrect statuses before learners report blocked access.</li>
                    <li>Document manual corrections in team notes for audit consistency.</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="delegated-admins" class="rounded-lg bg-white p-6 shadow">
        <h2 class="text-2xl font-semibold text-gray-900">4. Delegated Admin Management</h2>
        <p class="mt-2 text-gray-600">
            Delegated Admins should receive only the access required for their scope (for example, content-only or LMS-only administration).
        </p>

        <div class="mt-4 space-y-4 text-sm text-gray-700">
            <div class="rounded-lg border border-gray-200 p-4">
                <h3 class="text-lg font-semibold text-gray-900">4.1 Assign Roles</h3>
                <ol class="mt-2 list-decimal space-y-2 pl-5">
                    <li>Open <a class="font-medium text-blue-600 hover:text-blue-800" href="{{ route('admin.roles') }}">Roles & Permissions</a>.</li>
                    <li>Create or update a role (examples: Content Admin, LMS Admin).</li>
                    <li>Attach only needed permissions, then assign role to the selected admin user.</li>
                </ol>
            </div>

            <div class="rounded-lg border border-gray-200 p-4">
                <h3 class="text-lg font-semibold text-gray-900">4.2 User Access Hygiene</h3>
                <ol class="mt-2 list-decimal space-y-2 pl-5">
                    <li>Open <a class="font-medium text-blue-600 hover:text-blue-800" href="{{ route('admin.users') }}">Users</a> and review admin accounts monthly.</li>
                    <li>Remove unnecessary admin flags and stale role assignments promptly.</li>
                    <li>Never share accounts. Each delegated admin should have an individual login.</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="operations" class="rounded-lg bg-white p-6 shadow">
        <h2 class="text-2xl font-semibold text-gray-900">5. Operating Checklist</h2>
        <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div class="rounded-lg border border-gray-200 p-4">
                <h3 class="text-lg font-semibold text-gray-900">Daily</h3>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-gray-700">
                    <li>Check analytics spikes or drops.</li>
                    <li>Review event registrations and course enrollments.</li>
                    <li>Resolve broken links or content errors reported by users.</li>
                </ul>
            </div>
            <div class="rounded-lg border border-gray-200 p-4">
                <h3 class="text-lg font-semibold text-gray-900">Weekly</h3>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-gray-700">
                    <li>Review draft content and schedule upcoming posts/events.</li>
                    <li>Audit course quality and completion progress.</li>
                    <li>Confirm footer, contact, and social links are up to date.</li>
                </ul>
            </div>
            <div class="rounded-lg border border-gray-200 p-4">
                <h3 class="text-lg font-semibold text-gray-900">Monthly</h3>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-gray-700">
                    <li>Audit admin/delegated admin roles and permissions.</li>
                    <li>Export analytics PDF for reporting and governance.</li>
                    <li>Review email templates and campaign language for consistency.</li>
                </ul>
            </div>
            <div class="rounded-lg border border-gray-200 p-4">
                <h3 class="text-lg font-semibold text-gray-900">Before Major Announcements</h3>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-gray-700">
                    <li>Test key public pages and enrollment flow in incognito mode.</li>
                    <li>Verify mobile responsiveness for homepage and course pages.</li>
                    <li>Confirm event/course dates, times, and prices one final time.</li>
                </ul>
            </div>
        </div>
    </section>

    <section id="troubleshooting" class="rounded-lg bg-white p-6 shadow">
        <h2 class="text-2xl font-semibold text-gray-900">6. Troubleshooting</h2>
        <div class="mt-4 space-y-3 text-sm text-gray-700">
            <p><span class="font-semibold text-gray-900">Content update not visible:</span> Confirm item is published, clear browser cache, and verify the slug did not change unexpectedly.</p>
            <p><span class="font-semibold text-gray-900">Learner cannot access course:</span> Check enrollment status and payment record in Enrollments, then confirm lesson/course status is published.</p>
            <p><span class="font-semibold text-gray-900">Role changes not taking effect:</span> Reopen the user record, validate role assignment, and ask the user to sign out and sign back in.</p>
            <p><span class="font-semibold text-gray-900">Unexpected admin access issue:</span> Verify the account is marked as admin and has the expected delegated role assignment.</p>
        </div>
    </section>
</div>
@endsection
