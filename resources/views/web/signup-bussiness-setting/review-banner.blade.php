<section class="bg-white px-4 pt-6 md:px-10 md:pt-12 pb-0 desktop:px-80">
    <div class="bg-primary border-t-4 border-primary rounded-b text-white px-4 py-3 shadow bg-opacity-20" role="alert">
        <div class="flex">
            <div>
                <p class="card-heading text-primary text-center">
                    {{ isset($payment_setting) ? $payment_setting['review_confirm_heading'] : '' }}
                </p>
                <p>
                    {{ isset($payment_setting) ? $payment_setting['review_confirm_detail'] : '' }}
                </p>
            </div>
        </div>
    </div>
</section>
