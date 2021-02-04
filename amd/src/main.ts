/**
 * Amanote filter script.
 *
 * @copyright   2020 Amaplex Software
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/modal_factory'], ($, modalFactory) =>
{
class Main
{
  private params: IMoodleParams;
  private userParams: IMoodleUserParams;

  public init(rawUserParams: string): void
  {
    // Parse the params.
    this.params = window['amanote_params'];
    this.userParams = Main.parseParams(rawUserParams) as IMoodleUserParams;

    if (!this.params)
    {
      return;
    }

    // Add Amanote button to each file.
    this.addAmanoteButtonToFiles();
  }

  /**
   * Parse the given Moodle params.
   *
   * @param rawParams - The serialized params.
   *
   * @returns The parsed params as an object.
   */
  private static parseParams(rawParams: string): object
  {
    try
    {
      return JSON.parse(rawParams);
    }
    catch (error)
    {
      console.error(error);

      return null;
    }
  }

  /**
   * Add an Amanote button for each file in the current page.
   */
  private addAmanoteButtonToFiles(): void
  {
    // Append button to file resources.
    $('.modtype_resource').each((index, element) =>
    {
      // Get file id from element's id attribute (example 'module-1').
      const elementId = $(element).attr('id');

      if (!elementId || elementId.indexOf('module-') < 0)
      {
        return;
      }

      const moduleId = parseInt(elementId.replace('module-', ''), 10);
      const file = this.getFileByModuleId(moduleId);

      if (!file)
      {
        return;
      }

      // Append button.
      const button = this.generateAmanoteButton(file);

      $(element).find('a').css('display' , 'inline-block');
      $(element).find('.activityinstance').first().append(button);
    });

    // Append button to files in folders.
    $('.fp-filename-icon').each( (index, element) =>
    {
      // Get file id from file url.
      const fileLink = $(element).find('a').first();

      if (fileLink.length !== 1)
      {
        return;
      }

      const filePath = fileLink.attr('href');
      const file = this.getFileByURL(filePath);

      if (!file)
      {
        return;
      }

      // Append button.
      const button = this.generateAmanoteButton(file);

      fileLink.css('display' , 'inline-block');
      fileLink.after(button);
    });

    // Add click lister on the newly added buttons.
    setTimeout(() =>
    {
      $('.amanote-button').on('click',(event) =>
      {
        const fileId = $(event.currentTarget).attr('file-id');
        const file = this.getFileById(fileId);

        this.openModal(file);
      });
    }, 500);
  }

  /**
   * Generate a new button for a given file.
   *
   * @param file - The file for which the button should be created.
   *
   * @returns The JQuery generate button.
   */
  private generateAmanoteButton(file: IFile): JQuery
  {
    const a = $(`<a class="amanote-button" style="margin-left: 16px"><img src="${this.params.plugin.logo}" width="75px" alt="Open in Amanote"></a>`);
    a.css('display', 'inline-block');
    a.css('cursor', 'pointer');

    a.attr('file-id', file.id);

    return a;
  }

  /**
   * Open the modal for a given file.
   *
   * @param file - The file.
   */
  private openModal(file: IFile): void
  {
    if (!file)
    {
      return;
    }

    const modalParams = {
      title: 'Amanote',
      body: this.generateModalBodyHTML(file),
      footer: '',
    };

    modalFactory.create(modalParams)
      .then((modal) =>
      {
        modal.show();
      });
  }

  /**
   * Generate the modal body for a given file.
   *
   * @param file - The file.
   *
   * @returns The modal body in HTML.
   */
  private generateModalBodyHTML(file: IFile): string
  {
    const openInAmanoteURL = this.generateAmanoteURL(file, 'note-taking');
    const downloadNotesURL = `${openInAmanoteURL}&downloadNotes`;

    let body = Main.generateButtonHTML(openInAmanoteURL, this.params.strings.openInAmanote);
    body    += Main.generateButtonHTML(downloadNotesURL, this.params.strings.downloadNotes);

    if (this.userParams.isTeacher)
    {
      // Add Learning Analytics.
      const openAnalyticsURL = this.generateAmanoteURL(file, `document-analytics/${file.amaResourceId}/view`);
      body += Main.generateButtonHTML(openAnalyticsURL, this.params.strings.openAnalytics);

      // Add Podcast Creator.
      if (this.params.plugin.key)
      {
        const openPodcastCreatorURL = this.generateAmanoteURL(file, 'podcast/creator');
        body += Main.generateButtonHTML(openPodcastCreatorURL, this.params.strings.openPodcastCreator);
      }
    }

    return body;
  }

  /**
   * Generate a button.
   *
   * @param href - The button's href.
   * @param title - The button's title.
   *
   * @returns The button as HTML string.
   */
  private static generateButtonHTML(href: string, title: string): string
  {
    return `<a class="btn btn-secondary" tabindex="0" style="width: 100%; margin-top: 16px;" href="${href}" target="_blank">${title}</a>`;
  }

  /**
   * Generate an URL to open a file in Amanote.
   *
   * @param file - The file to open.
   * @param route - The route.
   *
   * @returns The generated url.
   */
  private generateAmanoteURL(file: IFile, route = 'note-taking'): string
  {
    if (!file)
    {
      return '';
    }

    // Parse the PDF path.
    const pdfPath = file.url
      .split('pluginfile.php')[1]
      .replace('content/0/', 'content/1/');

    // Generate the AMA path.
    const amaPath = this.params.privateFilePath + `${file.amaResourceId}` + '.ama';

    let protocol = 'https';
    if (this.params.siteURL.indexOf('https') < 0)
    {
      protocol = 'http';
    }

    return  protocol + '://app.amanote.com/' + this.params.language + '/moodle/' + route + '?' +
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
  }

  /**
   * Get a file by id.
   *
   * @param fileId - the file id.
   *
   * @returns The file.
   */
  private getFileById(fileId: number): IFile
  {
    const files = this.params.files || [];

    for (let i = 0; i < files.length; i++)
    {
      if (files[i].id == fileId)
      {
        return files[i];
      }
    }

    return null;
  }

  /**
   * Get a file by module id.
   *
   * @param moduleId - The module id.
   *
   * @returns The file.
   */
  private getFileByModuleId(moduleId: number): IFile
  {
    const files = this.params.files || [];
    for (let i = 0; i < files.length; i++)
    {
      if (files[i].module.id == moduleId)
      {
        return files[i];
      }
    }

    return null;
  }

  /**
   * Get a file by URL.
   *
   * @param url - The file URL.
   *
   * @returns The file.
   */
  private getFileByURL(url: string): IFile
  {
    const files = this.params.files || [];

    for (let i = 0; i < files.length; i++)
    {
      const path1 = files[i].url.split('pluginfile.php')[1];
      const path2 = url.split('pluginfile.php')[1].split('?')[0];

      if (path1 && path2 && path1 === path2)
      {
        return files[i];
      }
    }

    return null;
  }
}

  return new Main();
});

interface IFile
{
  module: {
    id: number,
    contextId: number;
    name: string;
    component: string;
  }
  id: number;
  name: string;
  path: string;
  url: string;
  amaResourceId: string;
}

interface IMoodleUserParams
{
  id: string;
  token: {
    value: string;
    expiration: number;
  },
  isTeacher: boolean;
}

interface IMoodleParams
{
  siteURL: string;
  language: string;
  privateFilePath: string;
  files: IFile[];
  moodle: {
    version: string;
  }
  plugin: {
    version: string;
    autosavePeriod: string;
    saveInProvider: string;
    key: string;
    logo: string;
  },
  strings: {
    openInAmanote: string;
    downloadNotes: string;
    openAnalytics: string;
    openPodcastCreator: string;
    teacher: string;
  }
}
