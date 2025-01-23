/*
 * @class View.Views.Base.SugarGatorDashlet
 * @alias SUGAR.App.view.views.BaseSugarGatorDashlet
 * @extends View.View
 */

({
    plugins: ['Dashlet'],

    _defaultOptions: {
        limit: 5,
        auto_refresh: 0
    },


    initialize: function(options) {
        options.meta = options.meta || {};
        this._super('initialize', [options]);
        this.meta = _.extend(this.meta, app.metadata.getView(null, this.name));
        //this.loadData(options.meta);
    },


    initDashlet: function() {
        this.model.fields.log_level.readonly = false;
        this.model.on('change:channel', this.loadData, this);
        this.context.attributes.model.on('change:channel', this.loadData, this);
        this.delegateButtonEvents();
    },


    delegateButtonEvents: function() {
        this.listenTo(this.context, 'button:update_configs:click', this.save);
    },


    loadData: function(options) {
        var callbacks = {
            success: _.bind(this.displayConfigs, this),
            error: _.bind(this.displayError, this)
        };

        var channel = this.model.get('channel');

        if (_.isEmpty(channel)) {
            return;
        }

        var params = {};
        var url = app.api.buildURL('SugarGator/' + channel, 'read', '', params);
        app.api.call('read', url, {}, callbacks);
    },


    displayConfigs: function(data) {
        app.alert.dismiss('sugar_gator_invalid_channel');
        _.extend(this, data);
        _.each(data, function(value, fieldName, list) {
            this.model.set(fieldName, value);
            }, this);

        if (this.model.get('channel') != 'sugarcrm') {
            var channel = 'not_sugarcrm';
        } else {
            var channel = 'sugarcrm';
        }

        let log_level = this.getField('log_level');
        //log_level.items = this.model.fields.log_level.visibility_grid.values[channel];

        // the items are actually cached, so we have to update the cache.
        let _itemsKey = 'cache:' + this.module + ':' + log_level.name + ':items';
        this.context.set(_itemsKey, this.model.fields.log_level.visibility_grid.values[channel]);
        //log_level.render();
        this.render();
    },


    displayError: function(error)
    {
        app.alert.show('sugar_gator_invalid_channel', {level: 'error', messages: 'LBL_SUGARGATOR_INVALID_CHANNEL'})
    },


    save: function() {
        app.alert.show('sugar_gator_update_in_progress', {level: 'info', autoClose: false, title: "Updating", messages: "Please wait while we update the configs."});
        let callbacks = {
        success:  _.bind(this.confirmUpdateAndResync, this),
        error: _.bind(this.displayError, this)


    };
        let params = {};
        params.log_level = this.model.get('log_level');
        params.max_num_records = this.model.get('max_num_records');
        params.prune_records_older_than_days = this.model.get('prune_records_older_than_days');
        var url = app.api.buildURL('SugarGator/' + this.model.get('channel'), 'update', '', {});
        app.api.call('update', url, params, callbacks);
    },


    confirmUpdateAndResync: function(data) {
        app.alert.dismiss('sugar_gator_update_in_progress');
        app.alert.show('sugar_gator_updated_configs', {level: 'success', autoClose: false, title: "Updated Configs", messages: "Configs have been updated. Please wait until your metadata re-syncs."});
        app.metadata.sync(_.bind(this.confirmMetadataHasResynced, this));
    },


    confirmMetadataHasResynced: function() {
        app.alert.dismiss('sugar_gator_updated_configs');
        app.alert.show('sugar_gator_resync_complete', {level: 'success', autoClose: true, title: "Resync Complete", messages: "Your metadata has been updated."});
    },

    /**
     * @inheritdoc
     *
     * New model related properties are injected into each model:
     *
     * - {Boolean} overdue True if record is prior to now.
     */
    _renderHtml: function() {
        if (this.meta.config) {
            this._super('_renderHtml');
            return;
        }

        this._super('_renderHtml');
    },
})
