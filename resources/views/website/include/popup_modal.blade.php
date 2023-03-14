<!-- Modal (pop up) -->
@if ($settingInfo->is_home_popup)
    @php
        $getPopups = App\Popup::where('is_active', 1)->get();
        $singlePopup = null;
        $singlePopup = sizeof($getPopups) > 0 ? $getPopups->random(1) : null;
    @endphp

    @if ($singlePopup)
        <!-- Home Popup Section -->
        <div class="modal fade subscribe_popup" id="onload-popup" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="ion-ios-close-empty"></i></span>
                        </button>
                        <div class="row g-0">
                            <div class="col-sm-12">
                                <div class="popup_content">
                                    <a href="{{ $singlePopup[0]->link ? $singlePopup[0]->link : '' }}" target="_blank">
                                        <div class="d-flex justify-content-center align-items-center h-100">
                                            <img src="{{ asset('/uploads/popup/' . $singlePopup[0]->image) }}" alt="popup banner">
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Screen Load Popup Section -->
    @endif
@endif
