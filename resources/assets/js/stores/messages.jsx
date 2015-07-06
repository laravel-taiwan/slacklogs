var Reflux = require('reflux'),
    request = require('superagent');

var Actions = require('../actions/app.jsx');

var MessageStore = Reflux.createStore({
    listenables: Actions,
    defaults: function() {
        return {
            channel: null,
            moreup: false,
            moredown: false,
            data: []
        };
    },
    init: function() {
        this.log = this.defaults();
    },
    getInitialState: function() {
        return this.log;
    },
    resetMessages: function() {
        this.log = this.defaults();
        this.trigger(this.log);
    },
    fetchChannelMore: function(channel, direction) {

    },
    fetchChannelLog: function(channel, date) {
        var dateString = date ? date.format('YYYY-MM-DD/HH:mm') : null,
            api = '/api/' + channel + (date ? '/' + dateString : '');

        request
            .get(api)
            .end(function(err, res) {
                if (res.ok) {
                    var meta = res.body.meta,
                        data = res.body.data;
                    this.log = {
                        channel: channel,
                        moreup: meta.moreup,
                        moredown: meta.moredown,
                        data: data
                    };

                    this.trigger(this.log);
                }
            }.bind(this));
    }
});

module.exports = MessageStore;
