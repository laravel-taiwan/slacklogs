var React = require('react/addons'),
    Reflux = require('reflux'),
    Router = require('react-router'),
    Link = Router.Link,
    _ = require('lodash'),
    moment = require('moment');

var Actions = require('../actions/app.jsx'),
    MessageStore = require('../stores/messages.jsx'),
    MessageCom = require('./partials/message.jsx'),
    DialogMixin = require('./mixins/dialog.jsx');

var ChannelCom = React.createClass({
    mixins: [
        Reflux.connect(MessageStore, 'log'),
        DialogMixin
    ],
    statics: {
        willTransitionTo: function(transition, params) {
            var channel = params.channel || '',
                date = params.date || '',
                time = params.time || '',
                datetime = date + ' ' + time,
                isDate = !_.isNaN(Date.parse(date + ' ' + time));

            if (! isDate && (date || time)) {
                transition.redirect('channel', { channel: 'general' });
            } else {
                datetime = date ? moment(new Date(datetime)) : null;

                console.log('fetchChannelLog');
                Actions.fetchChannelLog(channel, datetime);
            }
        },
        willTransitionFrom: function() {
            Action.resetMessages();
        }
    },
    generateLogs: function() {
        var data = this.state.log.data,
            channel = this.props.params.channel,
            logs = [],
            lastLog;

        console.log('props', this.props);

        _.forEach(data, function(message) {
            var lastDate = lastLog ? moment(_.parseInt(lastLog.ts * 1000)) : null,
                currDate = moment(_.parseInt(message.ts * 1000)),
                path = '/' + channel + '/' + currDate.format('YYYY-MM-DD/HH:mm') + '#log-';

            if (! lastDate || currDate.format('YYYY-MM-DD') !== lastDate.format('YYYY-MM-DD')) {
                logs = logs.concat(<li className="logs-day">{ currDate.format('dddd, DD MMM')  }</li>);
            }

            logs = logs.concat(<MessageCom key={ message.id } channel={ channel } message={ message } />);

            lastLog = message;
        });

        return logs;

    },
    render: function() {
        var logs = this.generateLogs();

        return (
            <section className="container">
                <main className="content">
                    <ul className="logs">
                        { logs }
                    </ul>
                </main>
                <aside />
            </section>
        );
    }
});

module.exports = ChannelCom;
