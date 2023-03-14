@extends('driver.include.master')
@section('title' , 'Assign orders to drivers')
@section('header')
    <style>
        .container {
            display:table;
            width: 100%;
            margin-top: -50px;
            padding: 50px 0 0 0; /*set left/right padding according to needs*/
            box-sizing: border-box;
        }
        header {
            background: green;
            height: 50px;
        }

        .row {
            height: 100%;
            display: table-row;
        }

        .row .cell {
            display: table-cell;
            float: none;
        }

        .cell-1 {
            background: pink;
            width: 25%;
        }
        .cell-2 {
            background: yellow;
            width: 75%;
        }
        .kt-footer {
            display: none !important;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="cell cell-1">Navigation</div>
            <div class="cell cell-2">Content</div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        function barcodeRead(barcode){
            $('.kt-quick-search__input').val(barcode);
            $('.kt-quick-search__form').submit();
        }

        function copyToClipboardLink(id) {
            var textBox = document.getElementById(id);
            textBox.select();
            document.execCommand("copy");
            toastr.success("Patyment Link Has Been Coppied");
        }
    </script>
@endsection