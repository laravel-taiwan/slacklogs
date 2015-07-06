var React = require('react/addons'),
    Router = require('react-router'),
    Link = Router.Link,
    _ = require('lodash'),
    moment = require('moment');

var DefaultMessage = React.createClass({
    render: function() {
        var message = this.props.message;

        return (
            <div>
            </div>
        );
    }
});

var MessageCom = React.createClass({
    getMoment: function() {
        var timestamp = _.parseInt(this.props.message.ts * 1000);

        return moment(timestamp);
    },
    getDateString: function() {
        return this.getMoment().format('YYYY-MM-DD');
    },
    getHour: function() {
        return this.getMoment().format('HH:mm');
    },
    getPath: function() {
        var channel = this.props.channel,
            message = this.props.message;

        return '/' + channel + '/' + this.getDateString() + '/' + this.getHour() + '#log-' + message.id;

    },
    getContent: function() {
        var message = this.props.message,
            subtype = message.subtype || 'message';

        switch (subtype) {
            case 'channel_join':

                break;
            default:
            return <span><span className="log-entry-username">{ message.user }</span>{ message.text }</span>;
        }
    },
    render: function() {
        var channel = this.props.channel,
            path = this.getPath(),
            message = this.props.message,
            licx = "log-entry new-log log-entry-" + (message.subtype || ''),
            content = this.getContent();

        return (
            <Link to={ path } className="logs-nav">
                <li className={ licx } id={ "log-" + message.id }>
                    <span className="log-entry-time">{ this.getHour() }</span>
                    <span className="log-entry-username">{ message.user }</span>：{ message.text }
                </li>
            </Link>
        );
    }
});

module.exports = MessageCom;
