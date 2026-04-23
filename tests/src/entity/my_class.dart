import 'package:uuid/uuid_value.dart';
import '/model/dayploy/dart_dto_bundle/tests/src/entity/foreign_class.dart';
import '/model/dayploy/dart_dto_bundle/tests/src/entity/int_values_enum.dart';
import '/model/dayploy/dart_dto_bundle/tests/src/entity/string_values_enum.dart';

class MyClass {
  final UuidValue id;
  final int numberInt;
  final double numberFloat;
  final DateTime maDate;
  final String name;
  final String? nullableString;
  final List<ForeignClass> foreignClasses;
  final List<int> references;
  final IntValuesEnum intEnum;
  final StringValuesEnum stringEnum;

  MyClass({
    required this.id,
    required this.numberInt,
    required this.numberFloat,
    required this.maDate,
    required this.name,
    this.nullableString,
    required this.foreignClasses,
    required this.references,
    required this.intEnum,
    required this.stringEnum,
  });
}
