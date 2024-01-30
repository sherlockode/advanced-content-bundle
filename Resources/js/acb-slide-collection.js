import slide from './slide';
import Collection from './acb-collection';

slide.element.on('slideContentUpdated', function() {
  Collection.init(slide.content);
});
