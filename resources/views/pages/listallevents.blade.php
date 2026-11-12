@extends('layouts.home')
@include('layouts.metatag_header')
@section('content')
@include('layouts.upcoming_events_header')

<section class="event-all-list">
    <div class="container-fluid">
        <div class="row d-flex justify-content-center">
            <div class="col-12 col-lg-10 mt-5">

                <form action="{{ route('filterevents') }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-3 col-lg-3 upcoming-events-start-date">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control form-control-sm" id="start_date" name="start_date" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3 col-lg-3 upcoming-events-end-date">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control form-control-sm" id="end_date" name="end_date" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-3 col-lg-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-secondary w-10 btn-sm all-events-button">Submit</button>
                    </div>
                </form>



                @if($listallevents->isEmpty())
                <div class="mt-5 mb-5 lh-1">
                    No events found for the selected date range.
                </div>
                @else
                <div class="table-container upcoming-events-table mt-5 mb-5 lh-1">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>EVENT NAME</th>
                                    <th>EVENT DATE</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($listallevents as $event)
                                <tr>
                                    <td>
                                        @if (@$event->url)
                                        <a href="{{ route('getAllSlug', ['slug' => @$event->url]) }}"
                                             class="upcoming-all-events-url">{{
                                            $event->title }}</a>
                                        @else
                                        {{ $event->title }}
                                        @endif
                                    </td>
                                    <td>{{ $event->formatted_date }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
                @endif



            </div>
        </div>
    </div>

</section>


@include('layouts.footer')
@endsection
