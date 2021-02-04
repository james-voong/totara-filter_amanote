/**
 * Amanote filter script.
 *
 * @copyright   2020 Amaplex Software
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/modal_factory'], function ($, modalFactory) {
    var Main = /** @class */ (function () {
        function Main() {
        }
        Main.prototype.init = function (rawUserParams) {
            // Parse the params.
            this.params = window['amanote_params'];
            this.userParams = Main.parseParams(rawUserParams);
            if (!this.params) {
                return;
            }
            // Add Amanote button to each file.
            this.addAmanoteButtonToFiles();
        };
        /**
         * Parse the given Moodle params.
         *
         * @param rawParams - The serialized params.
         *
         * @returns The parsed params as an object.
         */
        Main.parseParams = function (rawParams) {
            try {
                return JSON.parse(rawParams);
            }
            catch (error) {
                console.error(error);
                return null;
            }
        };
        /**
         * Add an Amanote button for each file in the current page.
         */
        Main.prototype.addAmanoteButtonToFiles = function () {
            var _this = this;
            // Append button to file resources.
            $('.modtype_resource').each(function (index, element) {
                // Get file id from element's id attribute (example 'module-1').
                var elementId = $(element).attr('id');
                if (!elementId || elementId.indexOf('module-') < 0) {
                    return;
                }
                var moduleId = parseInt(elementId.replace('module-', ''), 10);
                var file = _this.getFileByModuleId(moduleId);
                if (!file) {
                    return;
                }
                // Append button.
                var button = _this.generateAmanoteButton(file);
                $(element).find('a').css('display', 'inline-block');
                $(element).find('.activityinstance').first().append(button);
            });
            // Append button to files in folders.
            $('.fp-filename-icon').each(function (index, element) {
                // Get file id from file url.
                var fileLink = $(element).find('a').first();
                if (fileLink.length !== 1) {
                    return;
                }
                var filePath = fileLink.attr('href');
                var file = _this.getFileByURL(filePath);
                if (!file) {
                    return;
                }
                // Append button.
                var button = _this.generateAmanoteButton(file);
                fileLink.css('display', 'inline-block');
                fileLink.after(button);
            });
            // Add click lister on the newly added buttons.
            setTimeout(function () {
                $('.amanote-button').on('click', function (event) {
                    var fileId = $(event.currentTarget).attr('file-id');
                    var file = _this.getFileById(fileId);
                    _this.openModal(file);
                });
            }, 500);
        };
        /**
         * Generate a new button for a given file.
         *
         * @param file - The file for which the button should be created.
         *
         * @returns The JQuery generate button.
         */
        Main.prototype.generateAmanoteButton = function (file) {
            var a = $("<a class=\"amanote-button\" style=\"margin-left: 16px\"><img src=\"" + this.params.plugin.logo + "\" width=\"75px\" alt=\"Open in Amanote\"></a>");
            a.css('display', 'inline-block');
            a.css('cursor', 'pointer');
            a.attr('file-id', file.id);
            return a;
        };
        /**
         * Open the modal for a given file.
         *
         * @param file - The file.
         */
        Main.prototype.openModal = function (file) {
            if (!file) {
                return;
            }
            var modalParams = {
                title: 'Amanote',
                body: this.generateModalBodyHTML(file),
                footer: ''
            };
            modalFactory.create(modalParams)
                .then(function (modal) {
                modal.show();
            });
        };
        /**
         * Generate the modal body for a given file.
         *
         * @param file - The file.
         *
         * @returns The modal body in HTML.
         */
        Main.prototype.generateModalBodyHTML = function (file) {
            var openInAmanoteURL = this.generateAmanoteURL(file, 'note-taking');
            var downloadNotesURL = openInAmanoteURL + "&downloadNotes";
            var body = Main.generateButtonHTML(openInAmanoteURL, this.params.strings.openInAmanote);
            body += Main.generateButtonHTML(downloadNotesURL, this.params.strings.downloadNotes);
            if (this.userParams.isTeacher) {
                // Add Learning Analytics.
                var openAnalyticsURL = this.generateAmanoteURL(file, "document-analytics/" + file.amaResourceId + "/view");
                body += Main.generateButtonHTML(openAnalyticsURL, this.params.strings.openAnalytics);
                // Add Podcast Creator.
                if (this.params.plugin.key) {
                    var openPodcastCreatorURL = this.generateAmanoteURL(file, 'podcast/creator');
                    body += Main.generateButtonHTML(openPodcastCreatorURL, this.params.strings.openPodcastCreator);
                }
            }
            return body;
        };
        /**
         * Generate a button.
         *
         * @param href - The button's href.
         * @param title - The button's title.
         *
         * @returns The button as HTML string.
         */
        Main.generateButtonHTML = function (href, title) {
            return "<a class=\"btn btn-secondary\" tabindex=\"0\" style=\"width: 100%; margin-top: 16px;\" href=\"" + href + "\" target=\"_blank\">" + title + "</a>";
        };
        /**
         * Generate an URL to open a file in Amanote.
         *
         * @param file - The file to open.
         * @param route - The route.
         *
         * @returns The generated url.
         */
        Main.prototype.generateAmanoteURL = function (file, route) {
            if (route === void 0) { route = 'note-taking'; }
            if (!file) {
                return '';
            }
            // Parse the PDF path.
            var pdfPath = file.url
                .split('pluginfile.php')[1]
                .replace('content/0/', 'content/1/');
            // Generate the AMA path.
            var amaPath = this.params.privateFilePath + ("" + file.amaResourceId) + '.ama';
            var protocol = 'https';
            if (this.params.siteURL.indexOf('https') < 0) {
                protocol = 'http';
            }
            return protocol + '://app.amanote.com/' + this.params.language + '/moodle/' + route + '?' +
                'siteURL=' + this.params.siteURL + '&' +
                'accessToken=' + this.userParams.token.value + '&' +
                'tokenExpDate=' + this.userParams.token.expiration + '&' +
                'userId=' + this.userParams.id + '&' +
                'pdfPath=' + pdfPath + '&' +
                'amaPath=' + amaPath + '&' +
                'resourceId=' + file.amaResourceId + '&' +
                'autosavePeriod=' + this.params.plugin.autosavePeriod + '&' +
                'saveInProvider=' + this.params.plugin.saveInProvider + '&' +
                'providerVersion=' + this.params.moodle.version + '&' +
                'pluginVersion=' + this.params.plugin.version;
        };
        /**
         * Get a file by id.
         *
         * @param fileId - the file id.
         *
         * @returns The file.
         */
        Main.prototype.getFileById = function (fileId) {
            var files = this.params.files || [];
            for (var i = 0; i < files.length; i++) {
                if (files[i].id == fileId) {
                    return files[i];
                }
            }
            return null;
        };
        /**
         * Get a file by module id.
         *
         * @param moduleId - The module id.
         *
         * @returns The file.
         */
        Main.prototype.getFileByModuleId = function (moduleId) {
            var files = this.params.files || [];
            for (var i = 0; i < files.length; i++) {
                if (files[i].module.id == moduleId) {
                    return files[i];
                }
            }
            return null;
        };
        /**
         * Get a file by URL.
         *
         * @param url - The file URL.
         *
         * @returns The file.
         */
        Main.prototype.getFileByURL = function (url) {
            var files = this.params.files || [];
            for (var i = 0; i < files.length; i++) {
                var path1 = files[i].url.split('pluginfile.php')[1];
                var path2 = url.split('pluginfile.php')[1].split('?')[0];
                if (path1 && path2 && path1 === path2) {
                    return files[i];
                }
            }
            return null;
        };
        return Main;
    }());
    return new Main();
});
