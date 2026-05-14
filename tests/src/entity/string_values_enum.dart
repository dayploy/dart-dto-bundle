enum StringValuesEnum {
  none("none"),
  warning("warning"),
  error("error");

  final String value;
  const StringValuesEnum(this.value);

  factory StringValuesEnum.fromValue(String value) {
    return StringValuesEnum.values.firstWhere((e) => e.value == value);
  }
}
