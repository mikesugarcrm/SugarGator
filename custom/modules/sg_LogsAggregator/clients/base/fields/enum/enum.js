({
    extendsFrom: "EnumField",

    render: function() {

        if (this.name != 'log_level') {
            return this._super("render", arguments);
        }

        let visibilityGrid;
        visibilityGrid = this.def.visibility_grid || {};
        let hasTrigger = visibilityGrid.values && visibilityGrid.trigger && this.model.has(visibilityGrid.trigger);

        if (!hasTrigger) {
            return this._super("render", arguments);
        }

        let channel = this.model.get(visibilityGrid.trigger);
        if (channel != 'sugarcrm') {
            visibilityGrid.values[channel] = visibilityGrid.values['not_sugarcrm'];
        }

        this._super("render", arguments);
    },

    _setupKeysOrder: function() {

        var visibilityGrid;
        visibilityGrid = this.def.visibility_grid || {};
        var hasTrigger = visibilityGrid.values && visibilityGrid.trigger && this.model.has(visibilityGrid.trigger);

        if (hasTrigger) {
            // if we have a visibility grid, do not sort the options.
            return;
        }

        return this._super('_setupKeysOrder', arguments);
    }
})
