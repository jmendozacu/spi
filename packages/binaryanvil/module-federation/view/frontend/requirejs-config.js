var config = {
    map : {
        '*' : {
            'doT': "BinaryAnvil_Federation/js/lib/doT",
            'codebird': "BinaryAnvil_Federation/js/lib/codebird",
            'socialfeed': "BinaryAnvil_Federation/js/lib/jquery.socialfeed"
        }
    },

    shim: {
        socialfeed: ['jquery, doT, codebird, moment']
    }
};