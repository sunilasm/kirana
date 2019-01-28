jQuery(document).on('ajaxComplete', function (event, xhr, settings) {
    var sections,
        redirects;

    if (settings.type.match(/post|put|delete/i)) {
        sections = sectionConfig.getAffectedSections(settings.url);

        if (sections) {
            customerData.invalidate(sections);
            redirects = ['redirect', 'backUrl'];

            if (_.isObject(xhr.responseJSON) && !_.isEmpty(_.pick(xhr.responseJSON, redirects))) { //eslint-disable-line
                return;
            }
            customerData.reload(sections, true);
        }
    }
});