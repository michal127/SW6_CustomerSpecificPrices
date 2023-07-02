import template from './product-customer-prices-list.html.twig';

const {Component, Data: {Criteria}, Context} = Shopware;


Component.register('product-customer-prices-list', {
    template,
    inject: ['repositoryFactory', 'acl'],
    data: () => {
        return {
            isLoading: false,
            productCustomerPrices: []
        }
    },
    computed: {
        productId() {
            return this.$route.params.id;
        },
        productCustomerPriceRepository() {
            return this.repositoryFactory.create('md_product_customer_price');
        },
        productCustomerPriceCriteria() {
            return new Criteria()
                .addFilter(Criteria.equals('productId', this.productId))
                .addAssociation('customer');
        },
        detailRoute() {
            return 'md.customer.specific.prices.customerPriceEdit';
        },
        columns() {
            return [
                {
                    property: 'customer',
                    label: 'CustomerSpecificPrices.sw-product.detail.list.columns.customer',
                    allowResize: true,
                    routerLink: this.detailRoute
                },
                {
                    property: 'net',
                    label: 'CustomerSpecificPrices.sw-product.detail.list.columns.net',
                    allowResize: true,
                },
                {
                    property: 'gross',
                    label: 'CustomerSpecificPrices.sw-product.detail.list.columns.gross',
                    allowResize: true,
                },
                {
                    property: 'listNet',
                    label: 'CustomerSpecificPrices.sw-product.detail.list.columns.listNet',
                    allowResize: true,
                },
                {
                    property: 'listGross',
                    label: 'CustomerSpecificPrices.sw-product.detail.list.columns.listGross',
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
        },
        onAddNew() {
            this.$router.push({
                name: 'md.customer.specific.prices.customerPriceCreate',
                params: { productId: this.productId },
            });
        }
    },

    created() {
        this.fetchProductCustomerPrices();
    },
});