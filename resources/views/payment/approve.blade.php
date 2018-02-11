<style>
    .modal {
        text-align: center;
        padding: 0!important;
    }

    .modal:before {
        content: '';
        display: inline-block;
        height: 100%;
        vertical-align: middle;
        margin-right: -4px; /* Adjusts for spacing */
    }

    .modal-dialog {
        display: inline-block;
        text-align: left;
        vertical-align: middle;
    }
</style>

<div class="modal fade" id="approvePayment" tabindex="-1" role="dialog" aria-labelledby="approvePaymentLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="approvePaymentLabel">Assign Lots</h2>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            <div class="modal-footer">
                <input class="btn btn-primary" type="submit" name="confirm" value="Confirm">
                <input class="btn btn-primary" type="button" name="close" value="Close" data-dismiss="modal">
            </div>
        </div>
    </div>
</div>