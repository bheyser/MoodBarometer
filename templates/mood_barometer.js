(function($){

    var instances = [];

    $.fn.ilMoodBarometerEngine = function(barometerId, moodId, year, week, options)
    {
        options = jQuery.extend({}, jQuery.fn.ilMoodBarometerEngine.defaults, options);

        instances[barometerId] = new _ilMoodBarometerEngine(barometerId, moodId, year, week, options);

        return instances[barometerId];
    };

    $.fn.ilMoodBarometerEngine.defaults = {

        moodIdGood: '1',
        moodIdNeutral: '0',
        moodIdBad: '-1',

        moodInputSelector: 'div[data-type=moodInput]',
        moodInputDisabledClass: 'disabled',

        ajaxUrl: '#',
        ajaxParam: 'mood',

        freezeSelection: false
    };

    var _ilMoodBarometerEngine = function(barometerId, moodId, year, week, options)
    {
        this.barometerId = barometerId;
        this.selectedMood = moodId;
        this.curYear = year;
        this.curWeek = week;
        this.options = options;
    };

    _ilMoodBarometerEngine.prototype = {

        init: function()
        {
            if( this.selectedMood == '' || !this.options.freezeSelection )
            {
                this.attachMoodInputHandler();
            }
        },

        attachMoodInputHandler: function()
        {
            this.getMoodInputElements().each(function(pos, elem) {
                $(elem).on('click', handleMoodInput);
            });
        },

        detachMoodInputHandler: function()
        {
            this.getMoodInputElements().each(function(pos, elem) {
                $(elem).off('click');
            });
        },

        getMoodInputElements: function()
        {
            return this.getBarometerElement().find(this.options.moodInputSelector);
        },

        getBarometerElement: function()
        {
            return $('#'+this.barometerId);
        },

        sendMoodSelection: function(inputElement)
        {
            var data = {};

            data[this.options.ajaxParam] = {
                year: this.curYear,
                week: this.curWeek,
                moodId: $(inputElement).attr('data-id')
            };

            $.ajax(this.options.ajaxUrl, {
                method: 'post', data: data,
                success: sendMoodSuccessHandler
            });
        },

        moodSelectionSuccess: function(moodId)
        {
            if(this.options.freezeSelection)
            {
                this.detachMoodInputHandler();
            }

            this.updateMoodInputDisabling(moodId);
        },

        updateMoodInputDisabling: function(moodId)
        {
            var that = this;

            this.getMoodInputElements().each(function(pos, inputElement) {

                if( $(inputElement).attr('data-id') == moodId )
                {
                    $(inputElement).removeClass(that.options.moodInputDisabledClass);
                }
                else
                {
                    $(inputElement).addClass(that.options.moodInputDisabledClass);
                }
            });
        }
    };

    var sendMoodSuccessHandler = function(response)
    {
        instances[response.barometerId].moodSelectionSuccess(response.selectedMoodId);
    };

    var handleMoodInput = function()
    {
        instances[fetchBarometerId(this)].sendMoodSelection(this);
    };

    var fetchBarometerId = function(inputElement)
    {
        return $(inputElement).parent('.mood-barometer').attr('id');
    };

}(jQuery));
