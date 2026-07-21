<div class="table-responsive mt-3">
    <table class="table table-striped table-bordered align-middle">
        <thead class="thead-light">
            <tr>
                <th width="18%">DATE</th>
                <th width="12%">TIME</th>
                <th width="20%">MOVIE</th>
                <th>SNACKS NEEDED</th>
            </tr>
        </thead>
        <tbody>
            @forelse($shows as $show)
                <tr>
                    <td class="align-middle"><strong>{{ $show->show_date }}</strong></td>
                    <td class="align-middle">{{ $show->show_time }}</td>
                    <td class="align-middle">{{ $show->movie_name }}</td>
                    <td>
                        @foreach($show->snacks as $snackName => $sizes)
                            <div class="snack-box">
                                <strong>{{ $snackName }}</strong>
                                @foreach($sizes as $item)
                                    <span class="snack-badge">
                                        {{ $item->size }} : <span class="text-danger">{{ $item->qty }}</span>
                                    </span>
                                @endforeach
                            </div>
                        @endforeach
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-4">{{ $emptyText }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
