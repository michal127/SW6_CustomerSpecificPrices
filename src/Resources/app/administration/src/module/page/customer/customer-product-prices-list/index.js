import template from './customer-product-prices-list.html.twig';

const {Component, Data: {Criteria}, Context} = Shopware;


Component.register('customer-product-prices-list', {
    template,
    inject: ['repositoryFactory', 'acl'],
    data: () => {
        return {
            isLoading: false,
            productCustomerPrices: []
        }
    },
    computed: {
        customerId() {
            return this.$route.params.id;
        },
        productCustomerPriceRepository() {
            return this.repositoryFactory.create('md_product_customer_price');
        },
        productCustomerPriceCriteria() {
            return new Criteria()
                .addFilter(Criteria.equals('customerId', this.customerId))
                .addAssociation('product');
        },
        detailRoute() {
            return 'md.customer.specific.prices.customerPriceEdit';
        },
        columns() {
            return [
                {
                    property: 'product',
                    label: 'CustomerSpecificPrices.sw-customer.detail.list.columns.product',
                    allowResize: true,
                    routerLink: this.detailRoute
                },
                {
                    property: 'net',
                    label: 'CustomerSpecificPrices.sw-customer.detail.list.columns.net',
                    allowResize: true,
                },
                {
                    property: 'gross',
                    label: 'CustomerSpecificPrices.sw-customer.detail.list.columns.gross',
                    allowResize: true,
                },
                {
                    property: 'listNet',
                    label: 'CustomerSpecificPrices.sw-customer.detail.list.columns.listNet',
                    allowResize: true,
                },
                {
                    property: 'listGross',
                    label: 'CustomerSpecificPrices.sw-customer.detail.list.columns.listGross',
                    allowResize: true,
                },
            ];
        },
    },
    methods: {
        fetchProductCustomerPrices() {
            this.isLoading = true;
            this.productCustomerPriceRepository.search(this.productCustomerPriceCriteria, Context.api).then((response) => {
                this.productCustomerPrices = response;
            }).finally(() => {
                this.isLoading = false;
            });
        }
    },

    created() {
        this.fetchProductCustomerPrices();
    },
});