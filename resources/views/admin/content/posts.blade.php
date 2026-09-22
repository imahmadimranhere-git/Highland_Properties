<x-panel.box flush>
    <x-slot:actions>
        <a href="{{ route('admin.posts.create') }}" class="btn btn--primary btn--sm">
            <x-ui.icon name="plus" :size="16" /> New post
        </a>
    </x-slot:actions>

    @if ($posts->isEmpty())
        <x-ui.empty-state title="No posts yet" text="Market updates and buying guides help the site rank in search." />
    @else
        <div class="table-wrap">
            <table class="table-hp">
                <thead><tr><th>Title</th><th>Author</th><th>Status</th><th>Views</th><th>Updated</th><th class="is-actions">Actions</th></tr></thead>
                <tbody>
                    @foreach ($posts as $post)
                        <tr>
                            <td><strong>{{ $post->title }}</strong></td>
                            <td>{{ $post->author?->name ?? '—' }}</td>
                            <td>
                                @if ($post->is_published && $post->published_at?->isFuture())
                                    <span class="badge badge-navy">Scheduled {{ $post->published_at->format('d M') }}</span>
                                @else
                                    <span class="badge {{ $post->is_published ? 'badge-success' : 'badge-muted' }}">
                                        {{ $post->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ number_format($post->views_count) }}</td>
                            <td class="text-muted-hp">{{ $post->updated_at->format('d M Y') }}</td>
                            <td class="is-actions">
                                <a href="{{ route('admin.posts.edit', $post) }}" class="btn-icon" aria-label="Edit"><x-ui.icon name="pencil" :size="16" /></a>
                                <x-ui.delete-form :action="route('admin.posts.destroy', $post)" :confirm="'Delete the post &quot;' . $post->title . '&quot;?'" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $posts->links() }}
    @endif
</x-panel.box>
