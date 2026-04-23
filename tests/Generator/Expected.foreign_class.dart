import 'package:uuid/uuid_value.dart';
import '/model/dayploy/dart_dto_bundle/tests/src/entity/my_class.dart';

class ForeignClass {
  final UuidValue id;
  final MyClass? myClass;

  ForeignClass({
    required this.id,
    this.myClass,
  });
}
