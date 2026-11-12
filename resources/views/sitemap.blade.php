<?xml version="1.0" encoding="UTF-8"?>
<html>
<head>
    <title>Sitemap file: sitemap.xml</title>
</head>
<body>
    <h2>Sitemap file: sitemap.xml</h2>
    <p>Number of URLs in this sitemap: {{ count($sitemap) }}</p>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>URL Location</th>
                <th>Last Modification Date</th>
                <th>Priority</th>
            </tr>
        </thead>
        <tbody>
            @if ($sitemap && count($sitemap) > 0)
                @foreach ($sitemap as $item)
                    <tr>
                        <td>
                            @if ($item['alias'] != null)
                                <a href="{{ url($item['alias']) }}">{{ url($item['alias']) }}</a>
                            @else
                                <a href="{{ env('APP_URL') }}">{{ env('APP_URL') }}</a>
                            @endif
                        </td>
                        <td>
                            @php
                                $timestamp = $item['lastmod'];
                                $formattedDate = $timestamp > 0 ? \Carbon\Carbon::createFromTimestamp($timestamp)->format('Y-m-d\TH:i\Z') : '';
                            @endphp
                            {{ $formattedDate }}
                        </td>
                        <td>{{ $item['priority'] }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="4">No sitemap details available.</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
