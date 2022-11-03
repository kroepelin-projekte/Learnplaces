export class FluxUiMenuEvent {
  getName() {
    return this.constructor.name
  }
}

export class Created extends FluxUiMenuEvent {
  name = "created";
  collectionId = "";
}


export class HtmlLayoutRendered extends FluxUiMenuEvent {
  elementId = ""
}