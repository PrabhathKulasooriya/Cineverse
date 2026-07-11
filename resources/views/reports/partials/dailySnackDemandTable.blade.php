<div class="table-responsive mt-3">
    <table class="table table-striped table-bordered align-middle">
        <thead class="thead-light">
            <tr>
                <th width="25%">DATE</th>
                <th>TOTAL SNACKS NEEDED FOR THE DAY</th>
            </tr>
        </thead>
        <tbody>
            @forelse($days as $day)
                <tr>
                    <td class="align-middle" style="font-size: 15px;">
                        <strong>{{ $day->show_date }}</strong>
                    </td>
                    <td>
                        <div class="daily-snack-container">
                            @foreach($day->snacks as $snackName => $sizes)
                                <div class="snack-box snack-box-daily" >
                                    <strong>{{ $snackName }}</strong>
                                    
                                    @foreach($sizes as $item)
                                        <span class="snack-badge">
                                            {{ $item->size }} : <span class="text-danger">{{ $item->qty }}</span>
                                        </span>
                                    @endforeach
                                    
                                </div>
                            @endforeach
                            
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="2" class="text-center text-muted py-4">{{ $emptyText }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>