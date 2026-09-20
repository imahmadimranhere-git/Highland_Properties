{{-- One shared confirmation dialog for every destructive action in a panel. --}}
<dialog id="confirm-dialog" class="dialog">
    <div class="dialog__body">
        <h2 class="dialog__title">Confirm</h2>
        <p class="dialog__text" data-confirm-text></p>
    </div>
    <div class="dialog__foot">
        <button type="button" class="btn btn--secondary btn--sm" data-confirm-cancel>Cancel</button>
        <button type="button" class="btn btn--danger btn--sm" data-confirm-ok>Delete</button>
    </div>
</dialog>
