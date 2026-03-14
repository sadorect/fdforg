<?php

namespace App\Http\Controllers;

use App\Models\GalleryItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->string('type')->toString();
        $publishedItems = GalleryItem::query()
            ->published()
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('captured_at')
            ->get();

        $galleryStats = [
            'collection_count' => $publishedItems->count(),
            'image_count' => $publishedItems->sum(fn (GalleryItem $item) => count($item->normalized_image_paths)),
            'featured_count' => $publishedItems->where('is_featured', true)->count(),
        ];

        $typeSummaries = $publishedItems
            ->groupBy('type')
            ->map(fn ($group, $galleryType) => [
                'type' => $galleryType,
                'count' => $group->count(),
            ])
            ->sortBy('type')
            ->values();

        $baseQuery = GalleryItem::query()
            ->published()
            ->when($type !== '', function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('captured_at');

        $spotlightItem = (clone $baseQuery)->first();

        $items = (clone $baseQuery)
            ->when($spotlightItem, function ($query) use ($spotlightItem) {
                $query->whereKeyNot($spotlightItem->id);
            })
            ->paginate(18)
            ->withQueryString();

        $displayItems = collect([$spotlightItem])
            ->filter()
            ->merge($items->getCollection());

        $lightboxSlides = collect();
        $heroSlides = collect();
        $slideIndex = 0;

        foreach ($displayItems as $item) {
            $imageUrls = $item->image_urls;

            if (! empty($imageUrls) && $heroSlides->count() < 5) {
                $heroSlides->push([
                    'url' => $imageUrls[0],
                    'title' => $item->title,
                    'description' => $item->description,
                    'event_name' => $item->event_name,
                    'type' => ucfirst($item->type),
                    'slide_index' => $slideIndex,
                ]);
            }

            foreach ($imageUrls as $url) {
                $lightboxSlides->push([
                    'url' => $url,
                    'title' => $item->title,
                    'description' => $item->description,
                    'event_name' => $item->event_name,
                    'type' => ucfirst($item->type),
                ]);
                $slideIndex++;
            }
        }

        return view('gallery.index', [
            'items' => $items,
            'activeType' => $type,
            'typeSummaries' => $typeSummaries,
            'spotlightItem' => $spotlightItem,
            'galleryStats' => $galleryStats,
            'heroSlides' => $heroSlides->values(),
            'lightboxSlides' => $lightboxSlides,
        ]);
    }
}
