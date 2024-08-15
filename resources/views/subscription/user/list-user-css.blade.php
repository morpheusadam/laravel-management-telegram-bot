<style>
    .input-group-prepend{width: {{$width_normal}}% !important;padding-bottom: 8px;}
    .input-group-prepend:not(:last-child){padding-right: 8px;}
    .input-group-prepend input,.input-group-prepend select{width: 100% !important;}
    @media (max-width: 575.98px) {
        .input-group-prepend{width:50% !important;padding-right: 0 !important;}
        .input-group-prepend:nth-child(odd){padding-right: 8px !important;}
        @if(!$four_block)
            .input-group-prepend:last-child{width:100% !important;padding-right: 0px !important}
        @endif
        .input-group-prepend input,.input-group-prepend select{width: 100% !important;}
        .send_email_ui{width:100% !important;}
    }
</style>