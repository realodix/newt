@role('admin')
  <div class="row right_now">
    <div class="col-12 hint">
      <span class="all ml-2"><i class="fas fa-square"></i> @lang('All')</span>
      <span class="me ml-5"><i class="fas fa-square"></i> @lang('Me')</span>
      <span class="guest ml-5"><i class="fas fa-square"></i> @lang('Guest')</span>
    </div>
    <div class="col-md-6 col-xl-4" style="font-size: 1.25rem; font-weight: 300;">
      <div class="card border-left">
      <div class="card-body">
        <div class="row">
          <div class="col-6 col-sm-5"><b>@lang('Capacity')</b></div>
          <div class="col col-sm"><span title="{{number_format($capacity)}}" data-toggle="tooltip">{{numberFormatShort($capacity)}}</span></div>
        </div>
        <div class="row">
          <div class="col-6 col-sm-5"><b>@lang('Remaining')</b></div>
          <div class="col col-sm">
            <span title="{{number_format($remaining)}}" data-toggle="tooltip">
              {{numberFormatShort($remaining)}}
              ({{$remaining_percent}})
            </span>
          </div>
        </div>
      </div>
      </div>
    </div>
    <div class="col-md-6 col-xl text-center">
      <div class="card border-left">
      <div class="card-body">
        <div class="row">
          <div class="col-4">
            <div class="right_now-text--primary">
              <span title="{{number_format($totalShortUrl)}}" data-toggle="tooltip">{{numberFormatShort($totalShortUrl)}}</span>
            </div>
            <div class="right_now-label">@lang('Urls Shortened')</div>
          </div>
          <div class="col-4">
            <div class="right_now-text--secondary">
              <span title="{{number_format($totalShortUrlByMe)}}" data-toggle="tooltip">{{numberFormatShort($totalShortUrlByMe)}}</span>
            </div>
            <div class="right_now-label">@lang('Urls Shortened')</div>
          </div>
          <div class="col-4">
            <div class="right_now-text--tertiary">
              <span title="{{number_format($totalShortUrlByGuest)}}" data-toggle="tooltip">{{numberFormatShort($totalShortUrlByGuest)}}</span>
            </div>
            <div class="right_now-label">@lang('Urls Shortened')</div>
          </div>
        </div>
      </div>
      </div>
    </div>
  </div>

  <div class="row right_now">
    <div class="col-md-6 col-xl-4 text-center">
      <div class="card border-left">
      <div class="card-body">
        <div class="row">
          <div class="col-6">
            <div class="right_now-text--primary">
              <span title="{{number_format($totalUser)}}" data-toggle="tooltip">{{numberFormatShort($totalUser)}}</span>
            </div>
            <div class="right_now-label">@lang('Registered Users')</div>
          </div>
          <div class="col-6">
            <div class="right_now-text--primary">
              <span title="{{number_format($totalGuest)}}" data-toggle="tooltip">{{numberFormatShort($totalGuest)}}</span>
            </div>
            <div class="right_now-label">@lang('Guest')</div>
          </div>
        </div>
      </div>
      </div>
    </div>

    <div class="col-md-6 col-xl text-center">
      <div class="card border-left">
      <div class="card-body">
        <div class="row">
          <div class="col-4">
            <div class="right_now-text--primary">
              <span title="{{number_format($totalClicks)}}" data-toggle="tooltip">{{numberFormatShort($totalClicks)}}</span>
            </div>
            <div class="right_now-label">@lang('Clicks & Redirects')</div>
          </div>
          <div class="col-4">
            <div class="right_now-text--secondary">
              <span title="{{number_format($totalClicksByMe)}}" data-toggle="tooltip">{{numberFormatShort($totalClicksByMe)}}</span>
            </div>
            <div class="right_now-label">@lang('Clicks & Redirects')</div>
          </div>
          <div class="col-4">
            <div class="right_now-text--tertiary">
              <span title="{{number_format($totalClicksByGuest)}}" data-toggle="tooltip">{{numberFormatShort($totalClicksByGuest)}}</span>
            </div>
            <div class="right_now-label">@lang('Clicks & Redirects')</div>
          </div>
        </div>
      </div>
      </div>
    </div>

  </div>
@else
  <div class="row right_now">
    <div class="col-sm-6 col-lg-3 text-center">
      <div class="card border-left">
      <div class="card-body">
        <div class="right_now-text--primary">
          <span title="{{number_format($totalShortUrlByMe)}}" data-toggle="tooltip">{{numberFormatShort($totalShortUrlByMe)}}</span>
        </div>
        <div class="right_now-label">@lang('Urls Shortened')</div>
      </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3 text-center">
      <div class="card border-left">
      <div class="card-body">
        <div class="right_now-text--primary">
          <span title="{{number_format($totalClicksByMe)}}" data-toggle="tooltip">{{numberFormatShort($totalClicksByMe)}}</span>
        </div>
        <div class="right_now-label">@lang('Clicks & Redirects')</div>
      </div>
      </div>
    </div>
  </div>
@endrole
