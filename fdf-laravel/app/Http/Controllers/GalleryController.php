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

        $items = GalleryItem::query()
            ->published()
            ->when($type !== '', function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('captured_at')
            ->paginate(18)
            ->withQueryString();

        $availableTypes = GalleryItem::query()
            ->published()
            ->select('type')
            ->distinct()
            ->orderBy('type')
            ->pluck('type');

        $lightboxSlides = $items->getCollection()
            ->flatMap(function (GalleryItem $item) {
                return collect($item->image_urls)->map(function (string $url) use ($item) {
                    return [
                        'url' => $url,
                        'title' => $item->title,
                        'description' => $item->description,
                        'event_name' => $item->event_name,
                        'type' => ucfirst($item->type),
                    ];
                });
            })
            ->values();

        return view('gallery.index', [
            'items' => $items,
            'activeType' => $type,
            'availableTypes' => $availableTypes,
            'lightboxSlides' => $lightboxSlides,
        ]);
    }
}
