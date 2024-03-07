import $ from 'jquery';

class Slide
{
    constructor() {
        this.closeBtn = $('<button type="button" class="btn-close"><i class="fa-solid fa-xmark"></i></button>');
        this.backBtn = $('<button type="button" class="btn-back"><i class="fa-solid fa-arrow-left"></i></button>');
        this.buttons = $('<div class="slide-buttons"></div>');
        this.buttons.append(this.backBtn);
        this.buttons.append(this.closeBtn);
        this.header = $('<div class="acb-lateral-slide-header"></div>');
        this.content = $('<div class="acb-lateral-slide-content"></div>');
        this.footer = $('<div class="acb-lateral-slide-footer"></div>');
        this.element = $('<div class="acb-lateral-slide"></div>');
        this.element.append(this.buttons);
        this.element.append(this.header);
        this.element.append(this.content);
        this.element.append(this.footer);
        this.slideLayer = $('<div class="acb-lateral-slide-layer"></div>');

        let self = this;
        this.element.on('click', '.btn-cancel, .btn-close', function () {
            self.close();
        });
        this.slideLayer.on('click', function () {
            self.close();
        });
    }

    setHeader(html) {
        this.header.html(html)
    }

    setContent(html) {
        this.content.html(html);
        this.element.trigger('slideContentUpdated');
    }

    setFooter(html) {
        this.footer.html(html)
    }

    empty()
    {
        this.header.html('');
        this.content.html('');
        this.footer.html('');
        this.disableBackButton();
    }

    open() {
        if (!$('body').hasClass('acb-lateral-slide-open')) {
            $('body').append(this.slideLayer);
            $('body').append(this.element);
            setTimeout(() => $('body').addClass('acb-lateral-slide-open'), 10);
        }
    }

    close() {
        $('body').removeClass('acb-lateral-slide-open');
        this.disableBackButton();
    }

    enableBackButton(callback) {
      this.element.addClass('new-field-form');
      this.backBtn.on('click', callback);
    }
    disableBackButton() {
      this.element.removeClass('new-field-form');
      this.backBtn.off('click');
    }
}

let slide = new Slide();

export default slide;
