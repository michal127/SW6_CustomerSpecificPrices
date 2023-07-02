import template from './product-customer-prices-edit.html.twig';

const {Component, Context, Mixin} = Shopware;

const {mapPropertyErrors} = Component.getComponentHelper();
Component.register('product-customer-prices-edit', {
    template,
    mixins: [
        Mixin.getByName('notification')
    ],
    inject: ['repositoryFactory'],
    data: () => {
        return {
            isLoading: false,
            isSaving: false,
            customerPrice: null,
            product: null,
            productParent: null
        }
    },
    computed: {
        ...mapPropertyErrors('customerPrice', [
            'net',
            'gross',
            'listNet',
            'listGross',
            'customerId'
        ]),
        priceId() {
            return this.$route.params.id;
        },
        productCustomerPriceRepository() {
            return this.repositoryFactory.create('md_product_customer_price');
        },
        productRepository() {
            return this.repositoryFactory.create('product');
        },
        customerLabelCallback() {
            return (customer) => {
                if (!customer) {
                    return '';
                }
                return `${customer?.firstName} ${customer?.lastName} (${customer?.customerNumber})`;
            }
        },
        productName() {
            if (this.product?.name) {
                return this.product.name;
            }
            if (this.productParent?.name) {
                return this.productParent.name;
            }

            return '';
        }
    },
    methods: {
        onSave() {
            this.isSaving = true;
            return this.productCustomerPriceRepository.save(this.customerPrice, Context.api).then(() => {
                this.createNotificationSuccess({
                    message: this.$tc('CustomerSpecificPrices.sw-product.detail.edit.saveSuccess'),
                });
                this.fetchCustomerPrice();
            })
                .catch(() => {
                    this.createNotificationError({
                        message: this.$tc('CustomerSpecificPrices.sw-product.detail.edit.saveError'),
                    });
                })
                .finally(() => {
                    this.isSaving = false;
                });
        },
        fetchCustomerPrice() {
            this.isLoading = true;
            this.productCustomerPriceRepository.get(this.priceId, Context.api).then(async (entity) => {
                this.customerPrice = entity;
                await this.fetchProduct(entity.productId);
            }).finally(() => {
                this.isLoading = false;
            });
        },

        fetchProduct(productId) {
            return this.productRepository.get(productId, Context.api).then((entity) => {
                this.product = entity;
                if (this.product.parentId) {
                    this.fetchProductParent(this.product.parentId);
                }
            });
        },

        fetchProductParent(parentId) {
            return this.productRepository.get(parentId, Context.api).then((entity) => {
                this.productParent = entity;
            });
        }
    },

    created() {
        this.fetchCustomerPrice();
    },
});