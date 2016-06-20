/**
 * Copyright 2015 Polygant
 */
(function ($) {

    "use strict";

    if (typeof ko !== 'undefined' && ko.bindingHandlers && !ko.bindingHandlers.multiselect) {
        ko.bindingHandlers.multiselect = {
            init: function (element, valueAccessor, allBindingsAccessor, viewModel, bindingContext) {
            },
            update: function (element, valueAccessor, allBindingsAccessor, viewModel, bindingContext) {

                var config = ko.utils.unwrapObservable(valueAccessor());
                var selectOptions = allBindingsAccessor().options;
                var ms = $(element).data('multiselect');

                if (!ms) {
                    $(element).multiselect(config);
                }
                else {
                    ms.updateOriginalOptions();
                    if (selectOptions && selectOptions().length !== ms.originalOptions.length) {
                        $(element).multiselect('rebuild');
                    }
                }
            }
        };
    }

    function Multiselect(select, options) {
        this.$dataSelected = {}; //выбранные. Придется дублировать данные.
        this.$data = {}; //данные для оптимизации

        //Параметры селекта
        this.options = this.mergeOptions(options);
        //jQuery элемент select
        this.$select = $(select);
        // Initialization.
        // We have to clone to create a new reference.
        this.originalOptions = this.$select.clone()[0].options;
        this.query = '';
        this.searchTimeout = null;

        this.options.multiple = this.$select.attr('multiple') === "multiple";
        this.options.onChange = $.proxy(this.options.onChange, this);
        this.options.onDropdownShow = $.proxy(this.options.onDropdownShow, this);
        this.options.onDropdownHide = $.proxy(this.options.onDropdownHide, this);

        var ajaxDefault = {
            method: 'get',
            dataType: 'json',
        };

        var queryOptions = {
            value: 'id',
            label: 'name',
            init: true,
            minQueryLength: -1
        };
        this.options.ajax = $.extend({}, ajaxDefault, this.options.ajax);
        this.options.init = $.extend({}, ajaxDefault, this.options.init);

        if(this.isInit()){
            this.ajaxInit();
        }else if(this.isAjax()){
            this.ajaxUpdate();
        }else{
            this.getDataFromSelect();
        }
        // Build select all if enabled.
        this.buildContainer();
        this.buildButton();
        this.buildSelectAll();
        this.buildDropdown();
        this.buildDropdownOptions();
        this.buildFilter();
        this.updateButtonText();
        this.$select.hide().after(this.$container);
    }

    Multiselect.prototype = {

        // Default options.
        defaults: {
            // Default text function will either print 'None selected' in case no
            // option is selected, or a list of the selected options up to a length of 3 selected options by default.
            // If more than 3 options are selected, the number of selected options is printed.
            buttonText: function (options, select) {
                if (options.length === 0) {
                    return this.nonSelectedText + ' <b class="caret"></b>';
                }
                else {
                    if (options.length > this.numberDisplayed) {
                        return options.length + ' ' + this.nSelectedText + ' <b class="caret"></b>';
                    }
                    else {
                        var selected = '';
                        options.each(function () {
                            var label = ($(this).attr('label') !== undefined) ? $(this).attr('label') : $(this).html();

                            selected += label + ', ';
                        });
                        return selected.substr(0, selected.length - 2) + ' <b class="caret"></b>';
                    }
                }
            },
            // Like the buttonText option to update the title of the button.
            buttonTitle: function (options, select) {
                if (options.length === 0) {
                    return this.nonSelectedText;
                }
                else {
                    var selected = '';
                    options.each(function () {
                        selected += $(this).text() + ', ';
                    });
                    return selected.substr(0, selected.length - 2);
                }
            },
            // Create label
            label: function (element) {
                return $(element).attr('label') || $(element).html();
            },
            // Is triggered on change of the selected options.
            onChange: function (option, checked, unselect) {

            },
            // Triggered immediately when dropdown shown
            onDropdownShow: function (event) {

            },
            // Triggered immediately when dropdown hidden
            onDropdownHide: function (event) {

            },
            buttonClass: 'btn btn-default',
            dropRight: false,
            selectedClass: 'active',
            buttonWidth: 'auto',
            buttonContainer: '<div class="btn-group" />',
            // Maximum height of the dropdown menu.
            // If maximum height is exceeded a scrollbar will be displayed.
            maxHeight: false,
            includeSelectAllOption: false,
            selectAllText: ' Select all',
            selectAllValue: 'multiselect-all',
            enableFiltering: false,
            enableCaseInsensitiveFiltering: false,
            filterPlaceholder: 'Search',
            // possible options: 'text', 'value', 'both'
            filterBehavior: 'text',
            preventInputChangeEvent: false,
            nonSelectedText: 'None selected',
            nSelectedText: 'selected',
            numberDisplayed: 3,
            ajax: false,
            init: false,
            clearButton: false
        },

        // Templates.
        templates: {
            button: '<button type="button" class="multiselect dropdown-toggle" data-toggle="dropdown"></button>',
            button_reset: '<div class="input-group" style="width: 100%; padding: 0 10px 0 0"><span class="btn multiselect-reset" style="width: 100%">очистить</span></div>',
            ul: '<ul class="multiselect-container dropdown-menu"></ul>',
            filter: '<div class="input-group"><span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span><input class="form-control multiselect-search" type="text"><span class="input-group-addon btn clear-filter"><i class="glyphicon glyphicon-remove"></i></span></div>',
            filterEmpty: '<div class="input-group"></div>',
            li: '<li><a href="javascript:void(0);"><label></label></a></li>',
            divider: '<li class="divider"></li>',
            liGroup: '<li><label class="multiselect-group"></label></li>'
        },

        constructor: Multiselect,

        buildContainer: function () {
            this.$container = $(this.options.buttonContainer);
            this.$container.on('show.bs.dropdown', this.options.onDropdownShow);
            this.$container.on('hide.bs.dropdown', this.options.onDropdownHide);
        },

        buildButton: function () {
            // Build button.
            this.$button = $(this.templates.button).addClass(this.options.buttonClass);


            // Adopt active state.
            if (this.$select.prop('disabled')) {
                this.disable();
            }
            else {
                this.enable();
            }

            // Manually add button width if set.
            if (this.options.buttonWidth) {
                this.$button.css({
                    'width': this.options.buttonWidth
                });
            }

            // Keep the tab index from the select.
            var tabindex = this.$select.attr('tabindex');
            if (tabindex) {
                this.$button.attr('tabindex', tabindex);
            }

            this.$container.prepend(this.$button);
        },

        // Build dropdown container ul.
        buildDropdown: function () {

            // Build ul.
            this.$ul = $(this.templates.ul);

            if (this.options.dropRight) {
                this.$ul.addClass('pull-right');
            }

            // Set max height of dropdown menu to activate auto scrollbar.
            if (this.options.maxHeight) {
                // TODO: Add a class for this option to move the css declarations.
                this.$ul.css({
                    'max-height': this.options.maxHeight + 'px',
                    'overflow-y': 'auto',
                    'overflow-x': 'hidden',
                    'width': this.options.buttonWidth
                });
            }

            this.$container.append(this.$ul);
        },

        getDataFromSelect: function(){
            this.$data = {};
            //NB: only one level of optgroup is allowed! :)
            this.$select.children().each($.proxy(function (index, element) {
                // Support optgroups and options without a group simultaneously.
                var tag = $(element).prop('tagName')
                    .toLowerCase();


                if (tag === 'optgroup') {
                    this.parseDataGroup(element);
                }
                else if (tag === 'option') {

                    if ($(element).data('role') === 'divider') {
                        this.addDivider();
                    }
                    else {
                        this.parseDataOption(element);
                    }

                }
                // Other illegal tags will be ignored.
            }, this));
        },
        setDataToSelect: function(){
            var groups = [];
        },
        createSelectOption: function(dataItem,dataGroup){
            var selected = dataItem.selected?'selected="selected"':'';
            var optionDOM = '<option value="' + dataItem.value + '" '+selected+'>' + dataItem.name/*нафиг не надо, но раз положено...*/ + '</option>';
            if(typeof dataGroup === 'undefined') {
                this.$select.append(optionDOM);
            }else{
                this.$select.find('optgroup[label='+dataGroup+']').append(optionDOM);
            }
        },
        createSelectGroup: function(groupItem){
            var optgroup = $('<optgroup label="'+groupItem.label+'"></optgroup>');
            for(var i=0; i<groupItem.children.length; i++){
                this.createSelectOption(groupItem.children[i],groupItem.label);
            }
        },
        replaceSelected: function(){
            this.$dataSelected = {};
            for(var i in this.$data){
                if(this.$data.hasOwnProperty(i)) {
                    if (this.$data[i].selected) {
                        this.$dataSelected[i] = this.$data[i];
                    }
                }
            }
            this.updateForm();
        },
        updateSelected: function(){
            for(var i in this.$data){
                if(this.$data.hasOwnProperty(i)) {
                    if (this.$data[i].selected) {
                        this.setSelect(i);
                    }else if(this.$data[i].selected===false){
                        this.setUnselect(i);
                    }else if(this.$dataSelected[i] && this.$dataSelected[i].selected){
                        this.$data[i].selected = true;
                    }
                }
            }
            this.updateForm();
        },
        updateForm: function(){
            var optionDOM = "";
            var selected = "";
            for(var i in this.$dataSelected){
                if(this.$dataSelected.hasOwnProperty(i)){
                    var option = this.$dataSelected[i];
                    selected = option.selected?'selected="selected"':'';
                    optionDOM += '<option value="' + option.value + '" '+selected+'>' + option.name + '</option>';
                }
            }
            this.$select.html(optionDOM);
            this.$select.trigger('change');
        },
        // Build the dropdown and bind event handling.
        buildDropdownOptions: function () {
            var groups = {};
            var group_names = [];
            for(var i in this.$data){
                if(this.$data.hasOwnProperty(i) && this.$data[i]) {
                    if (!this.$data[i].group) {
                        this.createOptionValue(this.$data[i]);
                        continue;
                    }
                    if (typeof groups[this.$data[i].group] === 'undefined') {
                        groups[this.$data[i].group] = [];
                        group_names.push(this.$data[i].group);
                    }
                    groups[this.$data[i].group].push(this.$data[i]);
                }
            }
            for(i=0;i<group_names.length;i++){
                this.createOptgroup(groups[group_names[i]],group_names[i]);
            }
            // Bind the change event on the dropdown elements.
            $('li input', this.$ul).on('change', $.proxy(function (event) {
                var checked = $(event.target).prop('checked') || false;
                var isSelectAllOption = $(event.target).val() === this.options.selectAllValue;

                // Apply or unapply the configured selected class.
                if (this.options.selectedClass) {
                    if (checked) {
                        $(event.target).parents('li')
                            .addClass(this.options.selectedClass);
                    }
                    else {
                        $(event.target).parents('li')
                            .removeClass(this.options.selectedClass);
                    }
                }

                // Get the corresponding option.
                var value = $(event.target).val();
                var $option = this.getOptionByValue(value);

                var $optionsNotThis = $('option', this.$select).not($option);
                var $checkboxesNotThis = $('input', this.$container).not($(event.target));

                if (isSelectAllOption) {
                    if (this.$select[0][0].value === this.options.selectAllValue) {
                        var values = [];
                        var options = $('option[value!="' + this.options.selectAllValue + '"]', this.$select);
                        for (var i = 0; i < options.length; i++) {
                            // Additionally check whether the option is visible within the dropcown.
                            if (options[i].value !== this.options.selectAllValue && this.getInputByValue(options[i].value).is(':visible')) {
                                values.push(options[i].value);
                            }
                        }

                        if (checked) {
                            this.select(values);
                        }
                        else {
                            this.deselect(values);
                        }
                    }
                }

                if (checked) {
                    $option.prop('selected', true);

                    if (this.options.multiple) {
                        // Simply select additional option.
                        $option.prop('selected', true);
                    }
                    else {
                        // Unselect all other options and corresponding checkboxes.
                        if (this.options.selectedClass) {
                            $($checkboxesNotThis).parents('li').removeClass(this.options.selectedClass);
                        }

                        $($checkboxesNotThis).prop('checked', false);
                        $optionsNotThis.prop('selected', false);

                        // It's a single selection, so close.
                        this.$button.click();
                    }

                    if (this.options.selectedClass === "active") {
                        $optionsNotThis.parents("a").css("outline", "");
                    }

                    this.$data[$(event.target).val()].selected = true;
                    this.setSelect($(event.target).val());
                } else {
                    // Unselect option.
                    $option.prop('selected', false);

                    this.setUnselect($(event.target).val());
                }

                this.$select.change();
                this.options.onChange($option, checked, $.proxy(function(id){this.setUnselect(id);},this));
                this.updateButtonText();

                if (this.options.preventInputChangeEvent) {
                    return false;
                }
            }, this));
            $('li a', this.$ul).on('touchstart click', function (event) {
                event.stopPropagation();

                if (event.shiftKey) {
                    var checked = $(event.target).prop('checked') || false;

                    if (checked) {
                        var prev = $(event.target).parents('li:last')
                            .siblings('li[class="active"]:first');

                        var currentIdx = $(event.target).parents('li')
                            .index();
                        var prevIdx = prev.index();

                        if (currentIdx > prevIdx) {
                            $(event.target).parents("li:last").prevUntil(prev).each(
                                function () {
                                    $(this).find("input:first").prop("checked", true)
                                        .trigger("change");
                                }
                            );
                        }
                        else {
                            $(event.target).parents("li:last").nextUntil(prev).each(
                                function () {
                                    $(this).find("input:first").prop("checked", true)
                                        .trigger("change");
                                }
                            );
                        }
                    }
                }

                $(event.target).blur();
            });
            // Keyboard support.
            this.$container.on('keydown', $.proxy(function (event) {
                if ($('input[type="text"]', this.$container).is(':focus')) {
                    return;
                }
                if ((event.keyCode === 9 || event.keyCode === 27) && this.$container.hasClass('open')) {
                    // Close on tab or escape.
                    this.$button.click();
                }
                else {
                    var $items = $(this.$container).find("li:not(.divider):visible a");

                    if (!$items.length) {
                        return;
                    }

                    var index = $items.index($items.filter(':focus'));

                    // Navigation up.
                    if (event.keyCode === 38 && index > 0) {
                        index--;
                    }
                    // Navigate down.
                    else if (event.keyCode === 40 && index < $items.length - 1) {
                        index++;
                    }
                    else if (!~index) {
                        index = 0;
                    }

                    var $current = $items.eq(index);
                    $current.focus();

                    if (event.keyCode === 32 || event.keyCode === 13) {
                        var $checkbox = $current.find('input');

                        $checkbox.prop("checked", !$checkbox.prop("checked"));
                        $checkbox.change();
                    }

                    event.stopPropagation();
                    event.preventDefault();
                }
            }, this));
        },

        /**
         *
         * {
         * name: "Name",
         * value: "Value",
         * selected: true/false // false by default, of course:)
         * disabled: true/false //disabled+selected = can't unselect
         * order: int
         * type: row/group/divider
         * searchValue: "search value" //by default == name. If html = html.text(). Can be set manually
         * }
         *
         * @param options
         */
        addDataOption: function(options){
            this.$data[options.value] = options;
            if(options.selected){
                this.setSelect(options.value);
            }else if(options.selected === false){
                this.setUnselect(options.value);
            }
        },

        setSelect: function(value){
            this.$dataSelected[value] = this.$data[value];
            if(this.options.removeSelected) {
                delete this.$data[value];
                this.rebuild();
            }
            this.updateForm();
        },
        setUnselect: function(value){
            if(this.options.removeSelected && this.$dataSelected[value]) {
                this.$dataSelected[value].selected = null;
                this.$data[value] = this.$dataSelected[value];
                this.rebuild();
            }
            delete this.$dataSelected[value];
            this.updateForm();
        },

        // Will build an dropdown element for the given option.
        createOptionValue: function(dataItem) {
            if(dataItem.type==='divider'){
                this.addDivider();
                return;
            }
            // Support the label attribute on options.
            var label = dataItem.html?dataItem.html:dataItem.name;
            var value = dataItem.value;
            var inputType = this.options.multiple ? "checkbox" : "radio";

            var $li = $(this.templates.li);
            $('label', $li).addClass(inputType);
            $('label', $li).append('<input type="' + inputType + '" />');

            var selected = dataItem.selected || false;
            var $checkbox = $('input', $li);
            $checkbox.val(value);

            $('label', $li).append(" " + label);

            this.$ul.append($li);

            if (dataItem.disabled) {
                $checkbox.attr('disabled', 'disabled')
                    .prop('disabled', true)
                    .parents('li')
                    .addClass('disabled');
            }

            $checkbox.prop('checked', selected);

            if (selected && this.options.selectedClass) {
                $checkbox.parents('li')
                    .addClass(this.options.selectedClass);
            }
        },

        // Create divider
        createDivider: function (element) {
            var $divider = $(this.templates.divider);
            this.$ul.append($divider);
        },

        // Create optgroup.
        createOptgroup: function(group,label) {
            var groupName = label;

            // Add a header for the group.
            var $li = $(this.templates.liGroup);
            $('label', $li).text(groupName);

            this.$ul.append($li);

            // Add the options of the group.
            for(var i=0; i<group.length; i++) {
                this.createOptionValue(group[i]);
            }
        },

        // Add the select all option to the select.
        buildSelectAll: function () {
            var alreadyHasSelectAll = this.$select[0][0] ? this.$select[0][0].value === this.options.selectAllValue : false;

            // If options.includeSelectAllOption === true, add the include all checkbox.
            if (this.options.includeSelectAllOption && this.options.multiple && !alreadyHasSelectAll) {
                this.$select.prepend('<option value="' + this.options.selectAllValue + '">' + this.options.selectAllText + '</option>');
            }
        },
        // Build and bind filter.
        buildFilter: function () {

            // Build filter if filtering OR case insensitive filtering is enabled and the number of options exceeds (or equals) enableFilterLength.
            if (this.options.enableFiltering || this.options.enableCaseInsensitiveFiltering) {
                var enableFilterLength = Math.max(this.options.enableFiltering, this.options.enableCaseInsensitiveFiltering);
                var self=this;
                if ($('li', this.$ul).length >= enableFilterLength || this.isAjax()) {
                    this.$filter = $(this.templates.filter);


                    $('input', this.$filter).attr('placeholder', this.options.filterPlaceholder);


                    this.$ul.prepend(this.$filter);


                    this.$filter.val(this.query).on('click', function (event) {
                        event.stopPropagation();
                    }).on('keydown', $.proxy(function (event) {
                        // This is useful to catch "keydown" events after the browser has updated the control.
                        clearTimeout(this.searchTimeout);
                        this.searchTimeout = this.asyncFunction($.proxy(function () {
                            if (this.query !== event.target.value) {
                                this.query = event.target.value;

                                if (this.options.ajax && this.options.ajax.url) {
                                    var query = this.query;
                                    self.ajaxUpdate(query);
                                } else {
                                    $.each($('li', this.$ul), $.proxy(function (index, element) {
                                        var value = $('input', element).val();
                                        var text = $('label', element).text();

                                        if (value !== this.options.selectAllValue && text) {
                                            // by default lets assume that element is not
                                            // interesting for this search
                                            var showElement = false;

                                            var filterCandidate = '';
                                            if ((this.options.filterBehavior === 'text' || this.options.filterBehavior === 'both')) {
                                                filterCandidate = text;
                                            }
                                            if ((this.options.filterBehavior === 'value' || this.options.filterBehavior === 'both')) {
                                                filterCandidate = value;
                                            }

                                            if (this.options.enableCaseInsensitiveFiltering && filterCandidate.toLowerCase().indexOf(this.query.toLowerCase()) > -1) {
                                                showElement = true;
                                            }
                                            else if (filterCandidate.indexOf(this.query) > -1) {
                                                showElement = true;
                                            }

                                            if (showElement) {
                                                $(element).show();
                                            }
                                            else {
                                                $(element).hide();
                                            }
                                        }
                                    }, this));
                                }
                            }

                            // TODO: check whether select all option needs to be updated.
                        }, this), this.options.ajax ? 450 : 300, this);
                    }, this));
                    var clearSearch = $('.clear-filter', self.$filter);
                    clearSearch.on('click',function(){
                        $('input', self.$filter).val('').trigger('keydown');
                    });
                    var searchInput = $('input', this.$filter);
                    var strLength = this.query.length;
                    searchInput.focus();
                    searchInput.get(0).setSelectionRange(strLength, strLength);

                }


                if(this.options.clearButton) {
                    if (!this.$filter) {
                        this.$filter = $(this.templates.filterEmpty);
                        this.$ul.prepend(this.$filter);
                    }
                    this.$button_reset = $(this.templates.button_reset);
                    $('span', this.$button_reset).addClass(this.options.buttonClass);
                    this.$filter.after(this.$button_reset);
                    self.$button_reset.on('click', function () {
                        $('li.active input', self.$ul).trigger('click');
                    });
                }
            }
        },

        // Destroy - unbind - the plugin.
        destroy: function () {
            this.$container.remove();
            this.$select.show();
        },

        // Refreshs the checked options based on the current state of the select.
        refresh: function () {
            $('option', this.$select).each($.proxy(function (index, element) {
                var $input = $('li input', this.$ul).filter(function () {
                    return $(this).val() === $(element).val();
                });

                if ($(element).is(':selected')) {
                    $input.prop('checked', true);

                    if (this.options.selectedClass) {
                        $input.parents('li')
                            .addClass(this.options.selectedClass);
                    }
                }
                else {
                    $input.prop('checked', false);

                    if (this.options.selectedClass) {
                        $input.parents('li')
                            .removeClass(this.options.selectedClass);
                    }
                }

                if ($(element).is(":disabled")) {
                    $input.attr('disabled', 'disabled')
                        .prop('disabled', true)
                        .parents('li')
                        .addClass('disabled');
                }
                else {
                    $input.prop('disabled', false)
                        .parents('li')
                        .removeClass('disabled');
                }
            }, this));

            this.updateButtonText();
        },

        // Select an option by its value or multiple options using an array of values.
        select: function (selectValues) {
            if (selectValues && !$.isArray(selectValues)) {
                selectValues = [selectValues];
            }

            for (var i = 0; i < selectValues.length; i++) {
                var value = selectValues[i];

                var $option = this.getOptionByValue(value);
                var $checkbox = this.getInputByValue(value);

                if (this.options.selectedClass) {
                    $checkbox.parents('li')
                        .addClass(this.options.selectedClass);
                }

                $checkbox.prop('checked', true);
                $option.prop('selected', true);
                this.options.onChange($option, true, $.proxy(function(id){this.setUnselect(id);}(66),this));
            }
            this.updateButtonText();
        },

        // Deselect an option by its value or using an array of values.
        deselect: function (deselectValues) {
            if (deselectValues && !$.isArray(deselectValues)) {
                deselectValues = [deselectValues];
            }

            for (var i = 0; i < deselectValues.length; i++) {

                var value = deselectValues[i];

                var $option = this.getOptionByValue(value);
                var $checkbox = this.getInputByValue(value);

                if (this.options.selectedClass) {
                    $checkbox.parents('li')
                        .removeClass(this.options.selectedClass);
                }

                $checkbox.prop('checked', false);
                $option.prop('selected', false);
                this.options.onChange($option, false, $.proxy(function(id){this.setUnselect(id);}(66),this));
            }

            this.updateButtonText();
        },

        // Rebuild the whole dropdown menu.
        rebuild: function () {

            this.$ul.html('');
            var filterQuery;
            if (this.$filter) {
                filterQuery = $('input', this.$filter).val();
            } else {
                filterQuery = '';
            }
            // Remove select all option in select.
            //$('option[value="' + this.options.selectAllValue + '"]', this.$select).remove();

            // Important to distinguish between radios and checkboxes.
            this.options.multiple = this.$select.attr('multiple') === "multiple";

            this.updateSelected();
            this.buildSelectAll();
            this.buildDropdownOptions();
            this.updateButtonText();
            this.buildFilter();

            $('input', this.$filter).val(filterQuery);
        },

        // Build select using the given data as options.
        dataprovider: function (dataprovider) {
            var query = this.query;
            var optionDOM = "";
            var selected = "";
            dataprovider.forEach(function (option) {
                selected = option.selected?'selected="selected"':'';
                optionDOM += '<option value="' + option.value + '" '+selected+'>' + option.label + '</option>';
            });

            this.$select.html(optionDOM);
            this.rebuild();
            var searchInput = $('input', this.$filter);
            var strLength = query.length * 2;
            searchInput.val(query).focus();
            searchInput.get(0).setSelectionRange(strLength, strLength);
        },

        // Enable button.
        enable: function () {
            this.$select.prop('disabled', false);
            this.$button.prop('disabled', false)
                .removeClass('disabled');
        },

        // Disable button.
        disable: function () {
            this.$select.prop('disabled', true);
            this.$button.prop('disabled', true)
                .addClass('disabled');
        },

        // Set options.
        setOptions: function (options) {
            this.options = this.mergeOptions(options);
        },

        // Get options by merging defaults and given options.
        mergeOptions: function (options) {
            return $.extend({}, this.defaults, options);
        },

        // Update button text and button title.
        updateButtonText: function () {
            var options;
            if(!this.options.removeSelected) {
                options = this.getSelected();
            }else{
                options = [];
            }
            //
                // First update the displayed button text.
                $('button', this.$container).html(this.options.buttonText(options, this.$select));

                // Now update the title attribute of the button.
                $('button', this.$container).attr('title', this.options.buttonTitle(options, this.$select));
            //}
        },

        // Get all selected options.
        getSelected: function () {
            //var out = [];
            return $('option[value!="' + this.options.selectAllValue + '"]:selected', this.$select).filter(function () {
                return $(this).prop('selected');
            });
        },

        // Get the corresponding option by ts value.
        getOptionByValue: function (value) {
            var option = this.$data[value];
            if(option){
                return $('<option value="'+option.value+'">'+option.name+'</option>');
            }
            return {};
        },

        // Get an input in the dropdown by its value.
        getInputByValue: function (value) {
            return $('li input', this.$ul).filter(function () {
                return $(this).val() === value;
            });
        },

        updateOriginalOptions: function () {
            this.originalOptions = this.$select.clone()[0].options;
        },

        asyncFunction: function (callback, timeout, self) {
            var args = Array.prototype.slice.call(arguments, 3);
            return setTimeout(function () {
                callback.apply(self || window, args);
            }, timeout);
        },

        ajaxUpdate: function (query) {
            if(typeof query === 'undefined'){
                query = '';
            }
            var self = this;
            this.options.ajax.success = function (data) {
                self.$data = {};
                for(var i=0; i<data.length; i++){
                    if(data[i].id){
                        data[i].value=data[i].id;
                    }
                    self.$data[data[i].value] = data[i];
                }
                self.rebuild();
                if(typeof self.options.ajax.successCallback === 'function') {
                    self.options.ajax.successCallback(data);
                }
            };
            //Если не был переопределен запрос:
            if((typeof this.options.ajax.data === 'undefined')){
                this.options.ajax.data = {};
            }
            //if ((typeof this.options.ajax.data.q === 'undefined')){
            this.options.ajax.data.q = query;
            //}
            $.ajax(this.options.ajax);
        },
        ajaxInit: function () {
            var self = this;
            this.options.init.success = function (data) {
                self.$data = {};
                for (var i = 0; i < data.length; i++) {
                    if (data[i].id) {
                        data[i].value = data[i].id;
                    }
                    self.$data[data[i].value] = data[i];
                }
                self.rebuild();
                if (typeof self.options.ajax.successCallback === 'function') {
                    self.options.ajax.successCallback(data);
                }
            };
            $.ajax(this.options.init);
        },
        getOptionsFromSelect: function(){
            this.$select.children().each($.proxy(function (index, element) {
                // Support optgroups and options without a group simultaneously.
                var tag = $(element).prop('tagName')
                    .toLowerCase();

                if (tag === 'optgroup') {
                    this.createOptgroup(element);
                }
                else if (tag === 'option') {

                    if ($(element).data('role') === 'divider') {
                        this.createDivider();
                    }
                    else {
                        this.createOptionValue(element);
                    }

                }
                // Other illegal tags will be ignored.
            }, this));
        },
        parseDataOption: function(element,group){
            if ($(element).is(':selected')) {
                $(element).prop('selected', true);
            }
            if(typeof group === 'undefined'){
                group = null;
            }
            // Support the label attribute on options.
            var label = this.options.label(element);
            var value = $(element).val();
            var selected = $(element).prop('selected') || false;
            var disabled = $(element).is(':disabled');
            this.addDataOption({
                name: label,
                 value: value,
                 selected: selected,
                 disabled: disabled,
                 //order: int,
                 type: 'row',
                 searchValue: label,
                 group: group
            });
        },
        parseDataGroup: function(group){
            var groupName = $(group).prop('label');
            // Add the options of the group.
            $('option', group).each($.proxy(function (index, element) {
                this.parseDataOption(element,groupName);
            }, this));
        },
        addDivider: function(){
            this.addDataOption({type:'divider'});
        },
        isAjax: function(){
            return this.options.ajax && this.options.ajax.url;
        },
        isInit: function(){
            return this.options.init && this.options.init.url;
        }
    };

    $.fn.multiselect = function (option, parameter) {
        return this.each(function () {
            var data = $(this).data('multiselect');
            var options = typeof option === 'object' && option;

            // Initialize the multiselect.
            if (!data) {
                $(this).data('multiselect', ( data = new Multiselect(this, options)));
            }

            // Call multiselect method.
            if (typeof option === 'string') {
                data[option](parameter);
            }
        });
    };

    $.fn.multiselect.Constructor = Multiselect;

    // Automatically init selects by their data-role.
    $(function () {
        $("select[data-role=multiselect]").multiselect();
    });

})(window.jQuery);