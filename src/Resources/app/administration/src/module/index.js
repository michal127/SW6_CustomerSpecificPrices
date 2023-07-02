import './page';
import './overrides'

const { Module } = Shopware;

Module.register('md-customer-specific-prices', {
    type: 'plugin',
    name: 'MDCustomerSpecificPrices',
    title: 'CustomerSpecificPrices.general.mainMenuItemGeneral',
    description: 'CustomerSpecificPrices.general.descriptionTextModule',
    version: '1.0.0',
    targetVersion: '1.0.0',
    color: '#A092F0',
    icon: 'regular-shopping-bag',
    routes: {
        customerPriceCreate: {
            component: 'product-customer-prices-create',
            path: 'create/:productId',
            meta: {
                privilege: 'md_product_customer_price.creator',
                parentPath: 'sw.product.index'
            }
        },

        customerPriceEdit: {
            component: 'product-customer-prices-edit',
            path: 'edit/:id',
            meta: {
                privilege: 'md_product_customer_price.editor',
                parentPath: 'sw.product.index'
            }
        },
    },


    routeMiddleware(next, currentRoute) {
        if (currentRoute.name === 'sw.product.detail') {
            currentRoute.children.push({
                name: 'sw.product.detail.product_customer_prices_list',
                component: 'product-customer-prices-list',
                path: '/sw/product/detail/:id?/customer-prices',
                meta: {
                    parentPath: 'sw.product.index',
                    privilege: 'md_product_customer_price.viewer',
                },
            });
        }

        if (currentRoute.name === 'sw.customer.detail') {
            currentRoute.children.push({
                name: 'sw.customer.detail.product_customer_prices_list',
                component: 'customer-product-prices-list',
                path: '/sw/customer/detail/:id?/customer-prices',
                meta: {
                    parentPath: 'sw.customer.index',
                    privilege: 'md_product_customer_price.viewer',
                },
            });
        }
        next(currentRoute);
    },
});
