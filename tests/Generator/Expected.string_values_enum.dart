import 'package:collection/collection.dart';
enum StringValuesEnum {
  none("none"),
  warning("warning"),
  error("error");

  final String value;
  const StringValuesEnum(this.value);

  factory StringValuesEnum.fromValue(String value) {
    final enumValue = StringValuesEnum.values.firstWhereOrNull(
      (e) => e.value == value,
    );
    if (enumValue == null) {
      throw "ENUM StringValuesEnum HAS NO MAPPING FOR VALUE $value";
    }
    return enumValue;
  }
}
