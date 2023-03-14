<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
    <channel>
        <title>@if(!empty($settingInfo['name_'.app()->getLocale()])){{$settingInfo['name_'.app()->getLocale()]}}@endif</title>
        <description>@if(!empty($settingInfo['name_'.app()->getLocale()])){{$country->exists ? '('. $country['name_'.app()->getLocale()].')' : $settingInfo['name_'.app()->getLocale()]}}@endif</description>
        <link>{{url(app()->getLocale() == 'en' ? 'en' : 'ar')}}</link>


        @foreach($lines as $i => $line)
            @php
                $item = explode(config('excel.exports.csv.delimiter') , $line);
                foreach($keys as $i => $key) {
                    $item[$key] = $item[$i] ?? '';
                    unset($item[$i]);
                }
            @endphp

            {{--@php
                $item = explode(config('excel.exports.csv.delimiter') , $line);
                foreach ($values as $index => $value)
                    $line[$keys[$index]] = $item[$i] ?? '';
                }
            @endphp--}}
            <item>
                <g:id>{{$item['id']}}</g:id>
                <title>{{$item['title']}}</title>
                <description>{{$item['description']}}</description>
                <link>{{$item['link']}}</link>
                <g:price>{{$item['price']}}</g:price>
                <g:brand>{{@$item['brand']}}</g:brand>
                <g:condition>{{$item['condition']}}</g:condition>
                <g:availability>{{$item['availability']}}</g:availability>
                <g:image_link>{{$item['image_link']}}</g:image_link>
                <g:google_product_category>{{$item['google_product_category']}}</g:google_product_category>
                <g:gtin>{{$item['gtin']}}</g:gtin>
                <g:mpn>{{$item['mpn']}}</g:mpn>
                @foreach(explode(',', $item['additional_image_link']) as $additional_image_link)
                    <g:additional_image_link>{{$additional_image_link}}</g:additional_image_link>
                @endforeach
            </item>
        @endforeach
    </channel>
</rss>