import layoutHeader from './LayoutHeader.mjs';
import layoutHeaderMenu from './LayoutHeaderMenu.mjs';
import layoutHeaderMenuItem from './LayoutHeaderMenuItem.mjs';

const layoutItem = [];
layoutItem[layoutHeader.tagName] = layoutHeader.template;
layoutItem[layoutHeaderMenu.tagName] = layoutHeaderMenu.template;
layoutItem[layoutHeaderMenuItem.tagName] = layoutHeaderMenuItem.template;

export default layoutItem;