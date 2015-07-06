var React = require('react/addons'),
    Router = require('react-router'),
    RouteHandler = Router.RouteHandler;

var AppCom = React.createClass({
    render: function() {
        console.log('props', this.props);
        return (
            <div>
                <header className="header">
                    <a className="logo" href="http://laravel.tw/">
                        <img src="/img/laravel.png" />
                    </a>
                    <div className="links">
                        Power by <a href="https://github.com/farrrr" target="_blank">Far Tseng</a>
                    · <a href="https://github.com/laravel-taiwan/slacklogs" target="_blank">Fork us on Github</a>
                    </div>
                </header>
                <RouteHandler { ...this.state } />
            </div>
        );
    }
});

module.exports = AppCom;
