import { ClientAngular2CliPage } from './app.po';

describe('client-angular2-cli App', function() {
  let page: ClientAngular2CliPage;

  beforeEach(() => {
    page = new ClientAngular2CliPage();
  });

  it('should display message saying app works', () => {
    page.navigateTo();
    expect(page.getParagraphText()).toEqual('app works!');
  });
});
