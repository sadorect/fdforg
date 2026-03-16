<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\BlogPost;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use Carbon\Carbon;

class LMSSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks temporarily
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        // Clear existing data
        $this->clearExistingData();

        // Create users
        $this->createUsers();

        // Create categories
        $this->createCategories();

        // Create blog posts
        $this->createBlogPosts();

        // Create courses
        $this->createCourses();

        // Create lessons
        $this->createLessons();

        // Create enrollments
        $this->createEnrollments();

        // Create lesson progress
        $this->createLessonProgress();

        // Re-enable foreign key checks
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $this->command->info('LMS data seeded successfully!');
    }

    private function clearExistingData(): void
    {
        $tables = [
            'role_user',
            'permission_role',
            'roles',
            'permissions',
            'lesson_progress',
            'enrollments',
            'lessons',
            'courses',
            'blog_posts',
            'categories',
            'users',
        ];

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
    }

    private function createUsers(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@fdf.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_admin' => true,
        ]);

        // Instructor users
        $instructors = [
            ['name' => 'Sarah Johnson', 'email' => 'sarah@fdf.com', 'bio' => 'Expert in American Sign Language with 10+ years of teaching experience'],
            ['name' => 'Michael Chen', 'email' => 'michael@fdf.com', 'bio' => 'Specialist in deaf education and accessibility technology'],
            ['name' => 'Emily Rodriguez', 'email' => 'emily@fdf.com', 'bio' => 'Certified interpreter and advocate for deaf rights'],
        ];

        foreach ($instructors as $instructor) {
            User::create([
                'name' => $instructor['name'],
                'email' => $instructor['email'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'bio' => $instructor['bio'],
            ]);
        }

        // Regular students
        $students = [
            ['name' => 'John Smith', 'email' => 'john@example.com'],
            ['name' => 'Maria Garcia', 'email' => 'maria@example.com'],
            ['name' => 'David Lee', 'email' => 'david@example.com'],
            ['name' => 'Lisa Wang', 'email' => 'lisa@example.com'],
            ['name' => 'James Brown', 'email' => 'james@example.com'],
        ];

        foreach ($students as $student) {
            User::create([
                'name' => $student['name'],
                'email' => $student['email'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
        }

        $this->command->info('Created ' . (3 + 5 + 1) . ' users');
    }

    private function createCategories(): void
    {
        $categories = [
            [
                'name' => 'American Sign Language',
                'slug' => 'american-sign-language',
                'description' => 'Learn American Sign Language from basic to advanced levels',
                'type' => 'blog',
                'is_active' => true,
            ],
            [
                'name' => 'Deaf Culture',
                'slug' => 'deaf-culture',
                'description' => 'Explore the rich culture and history of the deaf community',
                'type' => 'blog',
                'is_active' => true,
            ],
            [
                'name' => 'Sign Language Basics',
                'slug' => 'sign-language-basics',
                'description' => 'Introduction to sign language for beginners',
                'type' => 'course',
                'is_active' => true,
            ],
            [
                'name' => 'Advanced Sign Language',
                'slug' => 'advanced-sign-language',
                'description' => 'Advanced techniques and fluency in sign language',
                'type' => 'course',
                'is_active' => true,
            ],
            [
                'name' => 'Deaf Education',
                'slug' => 'deaf-education',
                'description' => 'Educational approaches and methodologies for deaf students',
                'type' => 'course',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        $this->command->info('Created ' . count($categories) . ' categories');
    }

    private function createBlogPosts(): void
    {
        $blogCategory = Category::where('type', 'blog')->first();
        $asalCategory = Category::where('slug', 'american-sign-language')->first();
        $deafCultureCategory = Category::where('slug', 'deaf-culture')->first();

        $posts = [
            [
                'title' => 'Getting Started with American Sign Language',
                'slug' => 'getting-started-american-sign-language',
                'excerpt' => 'A comprehensive guide to beginning your journey into American Sign Language (ASL).',
                'content' => 'Learning American Sign Language is an incredible journey that opens up new ways of communication and connection with the deaf community. This guide will help you understand the basics of ASL, including finger spelling, basic vocabulary, and essential grammar rules. We\'ll explore the importance of facial expressions, body language, and the cultural context that makes ASL a rich and expressive language.',
                'category_id' => $asalCategory->id,
                'author_id' => 2, // Sarah Johnson
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(10),
                'views' => 245,
                'is_featured' => true,
                'tags' => json_encode(['ASL', 'beginner', 'finger spelling', 'basics']),
            ],
            [
                'title' => 'Understanding Deaf Culture: More Than Just a Language',
                'slug' => 'understanding-deaf-culture',
                'excerpt' => 'Explore the rich cultural heritage and traditions of the deaf community beyond sign language.',
                'content' => 'Deaf culture is a vibrant, diverse community with its own traditions, values, and art forms. This article delves into what makes deaf culture unique, from visual-gestural communication patterns to the importance of community gatherings and events. Learn about deaf artists, performers, and leaders who have shaped this culture over generations.',
                'category_id' => $deafCultureCategory->id,
                'author_id' => 3, // Michael Chen
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(7),
                'views' => 189,
                'is_featured' => false,
                'tags' => json_encode(['deaf culture', 'community', 'traditions', 'art']),
            ],
            [
                'title' => 'Tips for Effective ASL Practice',
                'slug' => 'tips-effective-asl-practice',
                'excerpt' => 'Practical strategies to improve your sign language skills and maintain fluency.',
                'content' => 'Consistent practice is key to becoming fluent in ASL. This guide provides proven techniques for daily practice, including finding practice partners, using online resources, attending deaf events, and incorporating sign language into your daily routine. We\'ll also cover common mistakes to avoid and how to track your progress effectively.',
                'category_id' => $asalCategory->id,
                'author_id' => 4, // Emily Rodriguez
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(5),
                'views' => 156,
                'is_featured' => false,
                'tags' => json_encode(['practice', 'fluency', 'improvement', 'daily routine']),
            ],
        ];

        foreach ($posts as $post) {
            BlogPost::create($post);
        }

        $this->command->info('Created ' . count($posts) . ' blog posts');
    }

    private function createCourses(): void
    {
        $courseCategories = Category::where('type', 'course')->get();

        $courses = [
            [
                'title' => 'Introduction to American Sign Language',
                'slug' => 'introduction-american-sign-language',
                'description' => 'Learn the fundamentals of ASL including basic vocabulary, finger spelling, and simple conversations.',
                'content' => 'This comprehensive introductory course covers all the essential foundations of American Sign Language. Students will learn basic vocabulary, finger spelling techniques, simple sentence structures, and cultural awareness. The course includes interactive video lessons, practice exercises, and live practice sessions.',
                'category_id' => $courseCategories->where('slug', 'sign-language-basics')->first()->id,
                'instructor_id' => 2, // Sarah Johnson
                'difficulty_level' => 'beginner',
                'duration_minutes' => 480, // 8 hours total
                'price' => 99.99,
                'status' => 'published',
                'max_students' => 50,
                'start_date' => Carbon::now()->addDays(7),
                'end_date' => Carbon::now()->addDays(37),
                'is_featured' => true,
                'is_certificate_enabled' => true,
                'enrollment_count' => 0,
                'rating' => 4.8,
                'review_count' => 25,
                'prerequisites' => json_encode([]),
                'learning_outcomes' => json_encode([
                    'Master basic ASL vocabulary',
                    'Learn proper finger spelling',
                    'Understand basic ASL grammar',
                    'Engage in simple conversations',
                    'Appreciate deaf culture',
                ]),
            ],
            [
                'title' => 'Advanced ASL Conversation Skills',
                'slug' => 'advanced-asl-conversation-skills',
                'description' => 'Take your ASL skills to the next level with advanced conversation techniques and cultural fluency.',
                'content' => 'This advanced course focuses on developing conversational fluency in ASL. Students will learn complex sentence structures, idiomatic expressions, storytelling techniques, and cultural nuances. The course includes advanced vocabulary, rapid signing practice, and real-world conversation scenarios.',
                'category_id' => $courseCategories->where('slug', 'advanced-sign-language')->first()->id,
                'instructor_id' => 4, // Emily Rodriguez
                'difficulty_level' => 'advanced',
                'duration_minutes' => 720, // 12 hours total
                'price' => 199.99,
                'status' => 'published',
                'max_students' => 30,
                'start_date' => Carbon::now()->addDays(14),
                'end_date' => Carbon::now()->addDays(44),
                'is_featured' => true,
                'is_certificate_enabled' => true,
                'enrollment_count' => 0,
                'rating' => 4.9,
                'review_count' => 18,
                'prerequisites' => json_encode(['Basic ASL knowledge', 'Completion of beginner course']),
                'learning_outcomes' => json_encode([
                    'Master advanced ASL vocabulary',
                    'Develop fluent conversation skills',
                    'Understand cultural nuances',
                    'Learn storytelling techniques',
                    'Achieve conversational fluency',
                ]),
            ],
            [
                'title' => 'Teaching Deaf Students: Educational Strategies',
                'slug' => 'teaching-deaf-students-educational-strategies',
                'description' => 'Essential strategies for educators working with deaf and hard-of-hearing students.',
                'content' => 'This course is designed for educators, parents, and professionals working with deaf students. Learn about deaf education methodologies, classroom accommodations, technology integration, and inclusive teaching practices. The course covers both theoretical foundations and practical applications.',
                'category_id' => $courseCategories->where('slug', 'deaf-education')->first()->id,
                'instructor_id' => 3, // Michael Chen
                'difficulty_level' => 'intermediate',
                'duration_minutes' => 600, // 10 hours total
                'price' => 149.99,
                'status' => 'published',
                'max_students' => 40,
                'start_date' => Carbon::now()->addDays(21),
                'end_date' => Carbon::now()->addDays(51),
                'is_featured' => false,
                'is_certificate_enabled' => true,
                'enrollment_count' => 0,
                'rating' => 4.7,
                'review_count' => 12,
                'prerequisites' => json_encode(['Basic teaching experience', 'Understanding of educational principles']),
                'learning_outcomes' => json_encode([
                    'Understand deaf education principles',
                    'Implement effective teaching strategies',
                    'Use assistive technology effectively',
                    'Create inclusive learning environments',
                    'Assess deaf student progress',
                ]),
            ],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }

        $this->command->info('Created ' . count($courses) . ' courses');
    }

    private function createLessons(): void
    {
        $courses = Course::all();

        foreach ($courses as $courseIndex => $course) {
            $lessons = $this->getCourseLessons($courseIndex + 1, $course->id);
            
            foreach ($lessons as $lessonData) {
                Lesson::create($lessonData);
            }
        }

        $this->command->info('Created lessons for all courses');
    }

    private function getCourseLessons(int $courseNumber, int $courseId): array
    {
        return match($courseNumber) {
            1 => [
                // Introduction to ASL lessons
                [
                    'title' => 'Welcome to ASL',
                    'slug' => 'welcome-to-asl',
                    'description' => 'Introduction to the course and ASL basics',
                    'content' => 'Welcome to your first ASL lesson! In this introduction, we\'ll cover course expectations, learning strategies, and the importance of facial expressions and body language in ASL communication.',
                    'type' => 'video',
                    'duration_minutes' => 45,
                    'sort_order' => 1,
                    'is_free' => true,
                    'is_published' => true,
                    'course_id' => $courseId,
                    'video_url' => 'https://www.youtube.com/watch?v=dRqfQaXqjOU',
                ],
                [
                    'title' => 'The ASL Alphabet',
                    'slug' => 'asl-alphabet',
                    'description' => 'Master finger spelling from A to Z',
                    'content' => 'Learn the complete ASL manual alphabet with proper hand shapes, positions, and movement patterns. Practice exercises included to build muscle memory.',
                    'type' => 'video',
                    'duration_minutes' => 60,
                    'sort_order' => 2,
                    'is_free' => false,
                    'is_published' => true,
                    'course_id' => $courseId,
                    'video_url' => 'https://www.youtube.com/watch?v=gy4r3l3b1pI',
                ],
                [
                    'title' => 'Basic Greetings and Introductions',
                    'slug' => 'basic-greetings-introductions',
                    'description' => 'Essential conversational phrases',
                    'content' => 'Learn how to greet people, introduce yourself, ask basic questions, and engage in simple conversations using proper ASL grammar and facial expressions.',
                    'type' => 'video',
                    'duration_minutes' => 55,
                    'sort_order' => 3,
                    'is_free' => false,
                    'is_published' => true,
                    'course_id' => $courseId,
                    'video_url' => 'https://www.youtube.com/watch?v=bYQhQoY7q8E',
                ],
                [
                    'title' => 'Numbers and Time',
                    'slug' => 'numbers-and-time',
                    'description' => 'Learn to sign numbers and express time',
                    'content' => 'Master number signs from 1-100, learn how to tell time, discuss dates, and use numerical expressions in everyday conversations.',
                    'type' => 'video',
                    'duration_minutes' => 50,
                    'sort_order' => 4,
                    'is_free' => false,
                    'is_published' => true,
                    'course_id' => $courseId,
                    'video_url' => 'https://www.youtube.com/watch?v=9rI8tOJ7xLk',
                ],
                [
                    'title' => 'Common Vocabulary: Family and Home',
                    'slug' => 'common-vocabulary-family-home',
                    'description' => 'Essential vocabulary for family members and home life',
                    'content' => 'Learn signs for family members, rooms in a house, household items, and activities related to home life.',
                    'type' => 'video',
                    'duration_minutes' => 65,
                    'sort_order' => 5,
                    'is_free' => false,
                    'is_published' => true,
                    'course_id' => $courseId,
                    'video_url' => 'https://www.youtube.com/watch?v=8N7L5n8tM3o',
                ],
                [
                    'title' => 'Basic ASL Grammar',
                    'slug' => 'basic-asl-grammar',
                    'description' => 'Understanding ASL sentence structure',
                    'content' => 'Learn the fundamental grammar rules of ASL, including word order, facial expressions, and non-manual markers.',
                    'type' => 'video',
                    'duration_minutes' => 70,
                    'sort_order' => 6,
                    'is_free' => false,
                    'is_published' => true,
                    'course_id' => $courseId,
                    'video_url' => 'https://www.youtube.com/watch?v=3K5wO5j5h2g',
                ],
                [
                    'title' => 'Practice Session: Conversations',
                    'slug' => 'practice-session-conversations',
                    'description' => 'Interactive practice exercises',
                    'content' => 'Put everything together in this comprehensive practice session. Practice conversations, receive feedback, and build confidence.',
                    'type' => 'interactive',
                    'duration_minutes' => 75,
                    'sort_order' => 7,
                    'is_free' => false,
                    'is_published' => true,
                    'course_id' => $courseId,
                    'video_url' => 'https://www.youtube.com/watch?v=6M4wO6h3k1p',
                ],
                [
                    'title' => 'Course Review and Next Steps',
                    'slug' => 'course-review-next-steps',
                    'description' => 'Review key concepts and plan your learning journey',
                    'content' => 'Comprehensive review of all concepts covered, assessment of your progress, and guidance for continuing your ASL learning journey.',
                    'type' => 'video',
                    'duration_minutes' => 60,
                    'sort_order' => 8,
                    'is_free' => false,
                    'is_published' => true,
                    'course_id' => $courseId,
                    'video_url' => 'https://www.youtube.com/watch?v=7J8pP7i4l2q',
                ],
            ],
            2 => [
                // Advanced ASL lessons
                [
                    'title' => 'Advanced Conversation Techniques',
                    'slug' => 'advanced-conversation-techniques',
                    'description' => 'Elevate your ASL conversation skills',
                    'content' => 'Master advanced conversational techniques including rapid signing, complex sentence structures, and natural flow in ASL conversations.',
                    'type' => 'video',
                    'duration_minutes' => 80,
                    'sort_order' => 1,
                    'is_free' => false,
                    'is_published' => true,
                    'course_id' => $courseId,
                    'video_url' => 'https://www.youtube.com/watch?v=9K9qQ9j5m3r',
                ],
                // Add more advanced lessons...
            ],
            3 => [
                // Deaf Education lessons
                [
                    'title' => 'Introduction to Deaf Education',
                    'slug' => 'introduction-deaf-education',
                    'description' => 'Understanding deaf education principles',
                    'content' => 'Comprehensive overview of deaf education history, methodologies, and best practices for teaching deaf and hard-of-hearing students.',
                    'type' => 'video',
                    'duration_minutes' => 90,
                    'sort_order' => 1,
                    'is_free' => false,
                    'is_published' => true,
                    'course_id' => $courseId,
                    'video_url' => 'https://www.youtube.com/watch?v=0L0lL0k6n4s',
                ],
                // Add more education lessons...
            ],
            default => [],
        };
    }

    private function createEnrollments(): void
    {
        $courses = Course::all();
        $students = User::where('email', '!=', 'admin@fdf.com')
                        ->where('email', '!=', 'sarah@fdf.com')
                        ->where('email', '!=', 'michael@fdf.com')
                        ->where('email', '!=', 'emily@fdf.com')
                        ->get();

        foreach ($courses as $course) {
            // Enroll random students in each course
            $maxEnrollment = min(8, $students->count()); // Don't request more students than available
            $enrollmentCount = rand(3, $maxEnrollment);
            $enrolledStudents = $students->random($enrollmentCount);

            foreach ($enrolledStudents as $student) {
                Enrollment::create([
                    'user_id' => $student->id,
                    'course_id' => $course->id,
                    'status' => 'active',
                    'enrolled_at' => Carbon::now()->subDays(rand(1, 30)),
                    'progress_percentage' => rand(0, 85),
                    'last_accessed_at' => Carbon::now()->subDays(rand(0, 7)),
                    'payment_status' => 'paid',
                    'paid_amount' => $course->price,
                ]);

                // Update course enrollment count
                $course->increment('enrollment_count');
            }
        }

        $this->command->info('Created enrollments for all courses');
    }

    private function createLessonProgress(): void
    {
        $enrollments = Enrollment::with(['course.lessons', 'user'])->get();

        foreach ($enrollments as $enrollment) {
            $lessons = $enrollment->course->lessons;
            $userProgress = $enrollment->progress_percentage;

            foreach ($lessons as $index => $lesson) {
                // Determine if this lesson should be completed based on overall progress
                $lessonThreshold = (($index + 1) / $lessons->count()) * 100;
                $isCompleted = $userProgress >= $lessonThreshold;
                
                $completionPercentage = $isCompleted ? 100 : rand(0, 80);
                $watchTime = ($lesson->duration_minutes * 60 * $completionPercentage) / 100;

                LessonProgress::create([
                    'user_id' => $enrollment->user_id,
                    'lesson_id' => $lesson->id,
                    'enrollment_id' => $enrollment->id,
                    'is_completed' => $isCompleted,
                    'completion_percentage' => $completionPercentage,
                    'watch_time_seconds' => $watchTime,
                    'started_at' => $enrollment->enrolled_at->addMinutes($index * 30),
                    'completed_at' => $isCompleted ? $enrollment->enrolled_at->addHours($index + 1) : null,
                    'last_accessed_at' => Carbon::now()->subMinutes(rand(0, 1440)), // Random time in last 24h
                ]);
            }
        }

        $this->command->info('Created lesson progress for all enrollments');
    }
}
