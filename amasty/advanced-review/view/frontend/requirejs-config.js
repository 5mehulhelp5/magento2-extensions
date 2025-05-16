var config = {
    map: {
        '*': {
            'amrevloader': 'Amasty_AdvancedReview/js/components/amrev-loader',
            'amReview': 'Amasty_AdvancedReview/js/amReview',
            'amSummaryInfo': 'Amasty_AdvancedReview/js/widget/amSummaryInfo',
            'amReviewSlider': 'Amasty_AdvancedReview/js/widget/amReviewSlider',
            'amImageSlider': 'Amasty_AdvancedReview/js/widget/amImageSlider',
            'amProductReviews': 'Amasty_AdvancedReview/js/widget/amProductReviews',
            'amShowMore': 'Amasty_AdvancedReview/js/components/am-show-more'
        }
    },
    config: {
        mixins: {
            'Magento_Review/js/view/review': {
                'Amasty_AdvancedReview/js/view/review': true
            }
        }
    },
    shim: {
        'Magento_Review/js/process-reviews': ['mage/tabs']
    }
};
