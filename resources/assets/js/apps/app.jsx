var React = require('react/addons'),
    Router = require('react-router'),
    DefaultRoute = Router.DefaultRoute,
    Route = Router.Route;

var AppCom = require('../components/app.jsx'),
    ChannelCom = require('../components/channel.jsx');

var routes = (
    <Route name="app" path="/" handler={ AppCom }>
        <Route name="channel" path=":channel" handler={ ChannelCom } />
        <Route name="date" path=":channel/:date" handler={ ChannelCom } />
        <Route name="datetime" path=":channel/:date/:time" handler={ ChannelCom } />
        <DefaultRoute handler={ ChannelCom } />
    </Route>
);

Router.run(routes, Router.HistoryLocation, function(Handler) {
    React.render(<Handler />, document.getElementById('mainSection'));
});
