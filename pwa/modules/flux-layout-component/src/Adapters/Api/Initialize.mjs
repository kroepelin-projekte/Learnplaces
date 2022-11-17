export default class Initialize {
  /** @var {string}} */
  behaviorsDirectoryPath;

  /**
   * @param {{string}} behaviorsDirectoryPath
   * @return this
   */
  static new(behaviorsDirectoryPath) {
    return new this(...behaviorsDirectoryPath);
  }

  /**
   * @param {{behaviorsDirectoryPath: string}} props
   */
  constructor(props) {
    this.behaviorsDirectoryPath = props.behaviorsDirectoryPath
  }

}
