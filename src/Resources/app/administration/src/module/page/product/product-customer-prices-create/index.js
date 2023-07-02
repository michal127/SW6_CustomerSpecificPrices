import template from './product-customer-prices-create.html.twig';

const {Component, Data: {Criteria}, Context} = Shopware;


Component.extend('product-customer-prices-create', 'product-customer-prices-edit', {
    template,


    methods: {
        onSave() {
            this.isSaving = true;
            return this.productCustomerPriceRepository.save(this.customerPrice, Context.api).then(() => {
                this.createNotificationSuccess({
                    message: this.$tc('CustomerSpecificPrices.sw-product.detail.create.saveSuccess'),
                });
                this.$router.push({
                    name: 'md.customer.specific.prices.customerPriceEdit',
                    params: { id: this.customerPrice.id },
                });
            }).catch(() => {
                this.createNotificationError({
                    message: this.$tc('CustomerSpecificPrices.sw-product.detail.create.saveError'),
                });
            }).finally(() => {
                this.isSaving = false;
            });
        },
        fetchCustomerPrice() {
            this.isLoading = true;
            const productId = this.$route.params.productId;
            this.customerPrice = this.productCustomerPriceRepository.create(Context.api);
            this.customerPrice.productId = productId;
            this.fetchProduct(productId).finally(() => {
                this.isLoading = false;
            });
        }
    },

});