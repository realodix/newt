@extends('layouts.backend')

@section('title', __('Statistics'))

@section('content')
<div class="statistics">
<div class="card">
<div class="card-body">

  <h3>UrlHub Statistics</h3>
<br>
  <b>Capacity</b>   : <span title="{{number_format($capacity)}}" data-toggle="tooltip">{{readable_int($capacity)}}</span> <br>
  <b>Remaining</b>  : <span title="{{number_format($remaining)}}" data-toggle="tooltip">
                        {{readable_int($remaining)}}
                        @if ($capacity == 0)
                          (0%)
                        @else
                          ({{round(100-((($capacity-$remaining)/$capacity)*100))}}%)
                        @endif
                      </span> <br>

<br>

  <b>Total Short Url</b> <br>
  Value             : <span title="{{number_format($totalShortUrl)}}" data-toggle="tooltip">{{readable_int($totalShortUrl)}}</span> <br>
  Value By Guest    : <span title="{{number_format($totalShortUrlByGuest)}}" data-toggle="tooltip">{{readable_int($totalShortUrlByGuest)}}</span> <br>

<br>

  <b>Total Clicks</b> <br>
  Value             : <span title="{{number_format($totalClicks)}}" data-toggle="tooltip">{{readable_int($totalClicks)}}</span> <br>
  Value By Guest    : <span title="{{number_format($totalClicksByGuest)}}" data-toggle="tooltip">{{readable_int($totalClicksByGuest)}}</span> <br>

<br>

  <b>Total User</b> <br>
  Registered User   : <span title="{{number_format($totalUser)}}" data-toggle="tooltip">{{readable_int($totalUser)}}</span> <br>
  Unregistered User : <span title="{{number_format($totalGuest)}}" data-toggle="tooltip">{{readable_int($totalGuest)}}</span> <br>


</div>
</div>
</div>
@endsection
