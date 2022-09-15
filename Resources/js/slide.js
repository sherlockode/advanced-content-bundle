import $ from 'jquery';

class Slide
{
    constructor() {
        this.element = $('<div class="acb-lateral-slide"><button type="button" class="btn-close"><i class="glyphicon glyphicon-remove"></i></button><div class="acb-lateral-slide-header"></div><div class="acb-lateral-slide-content"></div></div>');
        this.slideLayer = $('<div class="acb-lateral-slide-layer"></div>');
        this.header = this.element.find('.acb-lateral-slide-header');
        this.content = this.element.find('.acb-lateral-slide-content');

        let self = this;
        this.element.find('.btn-close').on('click', function () {
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
        this.content.html(html)
    }

    open() {
        $('body').append(this.slideLayer);
        $('body').append(this.element);
        setTimeout(() => $('body').addClass('acb-lateral-slide-open'), 10);
    }

    close() {
        $('body').removeClass('acb-lateral-slide-open');
    }
}

export default Slide;
