import 'package:uuid/uuid_value.dart';
import '/model/dayploy/dart_dto_bundle/tests/src/entity/my_class.dart';

class ForeignClass {
  late final UuidValue id;
  late final MyClass? myClass;

  ForeignClass({
    required this.id,
    this.myClass,
  });

  Map<String, dynamic> toJson() {
    return {
      "id": id.toString(),
      "myClass": myClass.toJson()?,
    };
  }
}
