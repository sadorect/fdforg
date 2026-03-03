<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PublicBlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_index_and_detail_are_available_for_published_posts(): void
    {
        $author = User::factory()->create(['is_admin' => true]);
        $category = Category::create([
            'name' => 'Updates',
            'slug' => 'updates',
            'type' => 'blog',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $published = BlogPost::create([
            'title' => 'Published Community Update',
            'slug' => 'published-community-update',
            'excerpt' => 'Published excerpt',
            'content' => 'Published content body',
            'category_id' => $category->id,
            'author_id' => $author->id,
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        BlogPost::create([
            'title' => 'Draft Community Update',
            'slug' => 'draft-community-update',
            'excerpt' => 'Draft excerpt',
            'content' => 'Draft content body',
            'category_id' => $category->id,
            'author_id' => $author->id,
            'status' => 'draft',
            'published_at' => now()->subDay(),
        ]);

        BlogPost::create([
            'title' => 'Scheduled Community Update',
            'slug' => 'scheduled-community-update',
            'excerpt' => 'Scheduled excerpt',
            'content' => 'Scheduled content body',
            'category_id' => $category->id,
            'author_id' => $author->id,
            'status' => 'published',
            'published_at' => now()->addDay(),
        ]);

        $this->get('/blog')
            ->assertOk()
            ->assertSee('Published Community Update')
            ->assertDontSee('Draft Community Update')
            ->assertDontSee('Scheduled Community Update');

        $this->get('/blog/' . $published->slug)
            ->assertOk()
            ->assertSee('Published content body');

        $this->get('/blog/draft-community-update')->assertNotFound();
        $this->get('/blog/scheduled-community-update')->assertNotFound();
    }

    public function test_homepage_and_navigation_include_public_blog_links(): void
    {
        $author = User::factory()->create(['is_admin' => true]);
        $category = Category::create([
            'name' => 'Stories',
            'slug' => 'stories',
            'type' => 'blog',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        BlogPost::create([
            'title' => 'Homepage Linked Story',
            'slug' => 'homepage-linked-story',
            'excerpt' => 'Story excerpt',
            'content' => 'Story content body',
            'category_id' => $category->id,
            'author_id' => $author->id,
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        Page::create([
            'title' => 'Home',
            'slug' => 'home',
            'content' => 'Home page content',
            'status' => 'published',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Visit blog')
            ->assertSee('/blog/homepage-linked-story', false)
            ->assertSee('>Blog<', false);
    }

    public function test_blog_detail_handles_string_tags_without_error(): void
    {
        $author = User::factory()->create(['is_admin' => true]);
        $category = Category::create([
            'name' => 'Highlights',
            'slug' => 'highlights',
            'type' => 'blog',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        DB::table('blog_posts')->insert([
            'title' => 'String Tags Story',
            'slug' => 'string-tags-story',
            'excerpt' => 'A story with string tags',
            'content' => 'String tag content body',
            'status' => 'published',
            'published_at' => now()->subHour(),
            'author_id' => $author->id,
            'category_id' => $category->id,
            'tags' => 'accessibility,community',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->get('/blog/string-tags-story')
            ->assertOk()
            ->assertSee('String tag content body')
            ->assertSee('accessibility')
            ->assertSee('community');
    }
}
