import 'package:uuid/uuid_value.dart';

class AutorefClass {
  late final UuidValue id;
  late final AutorefClass? autoref;

  AutorefClass({
    required this.id,
    this.autoref,
  });

}
