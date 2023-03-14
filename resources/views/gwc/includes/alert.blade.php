@if(Session::get('message-success'))
<div data-notify="container" class="alert m-alert animated fadeInDown alert-success" role="alert"
	data-notify-position="top-right"
	style="display: inline-block; margin: 0px auto; position: fixed; transition: all 0.5s ease-in-out 0s; z-index: 1031; top: 20px; right: 20px;">
	<button type="button" aria-hidden="true" class="close" data-notify="dismiss"
		style="position: absolute; right: 10px; top: 5px; z-index: 1033;"></button>
	<span data-notify="icon"></span>
	<span data-notify="title"></span>
	<span data-notify="message">{{ Session::get('message-success') }}</span>
	<a href="#" target="_blank" data-notify="url"></a>
</div>
@endif
@if(Session::get('message-error'))
<div data-notify="container" class="alert m-alert animated fadeInDown alert-danger" role="alert"
	data-notify-position="top-right"
	style="display: inline-block; margin: 0px auto; position: fixed; transition: all 0.5s ease-in-out 0s; z-index: 1031; top: 20px; right: 20px;">
	<button type="button" aria-hidden="true" class="close" data-notify="dismiss"
		style="position: absolute; right: 10px; top: 5px; z-index: 1033;"></button>
	<span data-notify="icon"></span>
	<span data-notify="title"></span>
	<span data-notify="message">{{ Session::get('message-error') }}</span>
	<a href="#" target="_blank" data-notify="url"></a>
</div>
@endif
@if(count($errors) > 0 )
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <ul class="p-0 m-0" style="list-style: none;">
        @foreach($errors->all() as $error)
        <li>{{$error}}</li>
        @endforeach
    </ul>
</div>
@endif
<!--default modal -->
<div class="modal fade" id="kt_modal_default_gulfweb" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><span id="response_title"></span></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<span id="response_message"></span>
			</div>
		</div>
	</div>
</div>
