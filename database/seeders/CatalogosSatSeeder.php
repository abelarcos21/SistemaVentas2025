<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FormaPago;
use App\Models\MetodoPago;
use App\Models\UsoCfdi;
use App\Models\RegimenFiscal;
use App\Models\ObjetoImpuesto;
use Illuminate\Support\Facades\DB;

class CatalogosSatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('formas_pago')->insert([
            ['clave' => '01', 'descripcion' => 'Efectivo'],
            ['clave' => '02', 'descripcion' => 'Cheque nominativo'],
            ['clave' => '03', 'descripcion' => 'Transferencia electrónica de fondos'],
            ['clave' => '04', 'descripcion' => 'Tarjeta de crédito'],
            ['clave' => '05', 'descripcion' => 'Monedero electrónico'],
            ['clave' => '06', 'descripcion' => 'Dinero electrónico'],
            ['clave' => '08', 'descripcion' => 'Vales de despensa'],
            ['clave' => '12', 'descripcion' => 'Dación en pago'],
            ['clave' => '13', 'descripcion' => 'Pago por subrogación'],
            ['clave' => '14', 'descripcion' => 'Pago por consignación'],
            ['clave' => '15', 'descripcion' => 'Condonación'],
            ['clave' => '17', 'descripcion' => 'Compensación'],
            ['clave' => '23', 'descripcion' => 'Novación'],
            ['clave' => '24', 'descripcion' => 'Confusión'],
            ['clave' => '25', 'descripcion' => 'Remisión de deuda'],
            ['clave' => '26', 'descripcion' => 'Prescripción o caducidad'],
            ['clave' => '27', 'descripcion' => 'A satisfacción del acreedor'],
            ['clave' => '28', 'descripcion' => 'Tarjeta de débito'],
            ['clave' => '29', 'descripcion' => 'Tarjeta de servicios'],
            ['clave' => '30', 'descripcion' => 'Aplicación de anticipos'],
            ['clave' => '31', 'descripcion' => 'Intermediario pagos'],
            ['clave' => '99', 'descripcion' => 'Por definir'],
        ]);

        DB::table('metodos_pago')->insert([

            ['clave' => 'PUE', 'descripcion' => 'Pago en una sola exhibición'],
            ['clave' => 'PPD', 'descripcion' => 'Pago en parcialidades o diferido'],
        ]);

        DB::table('usos_cfdi')->insert([
          ['clave' => 'G01', 'descripcion' => 'Adquisición de mercancías', 'persona_fisica' => true, 'persona_moral' => true],
            ['clave' => 'G02', 'descripcion' => 'Devoluciones, descuentos o bonificaciones', 'persona_fisica' => true, 'persona_moral' => true],
            ['clave' => 'G03', 'descripcion' => 'Gastos en general', 'persona_fisica' => true, 'persona_moral' => true],
            ['clave' => 'I01', 'descripcion' => 'Construcciones', 'persona_fisica' => false, 'persona_moral' => true],
            ['clave' => 'I02', 'descripcion' => 'Mobiliario y equipo de oficina por inversiones', 'persona_fisica' => false, 'persona_moral' => true],
            ['clave' => 'I03', 'descripcion' => 'Equipo de transporte', 'persona_fisica' => false, 'persona_moral' => true],
            ['clave' => 'I04', 'descripcion' => 'Equipo de cómputo y accesorios', 'persona_fisica' => false, 'persona_moral' => true],
            ['clave' => 'I05', 'descripcion' => 'Dados, troqueles, moldes, matrices y herramental', 'persona_fisica' => false, 'persona_moral' => true],
            ['clave' => 'I06', 'descripcion' => 'Comunicaciones telefónicas', 'persona_fisica' => false, 'persona_moral' => true],
            ['clave' => 'I07', 'descripcion' => 'Comunicaciones satelitales', 'persona_fisica' => false, 'persona_moral' => true],
            ['clave' => 'I08', 'descripcion' => 'Otra maquinaria y equipo', 'persona_fisica' => false, 'persona_moral' => true],
            ['clave' => 'D01', 'descripcion' => 'Honorarios médicos, dentales y gastos hospitalarios.', 'persona_fisica' => true, 'persona_moral' => false],
            ['clave' => 'D02', 'descripcion' => 'Gastos médicos por incapacidad o discapacidad', 'persona_fisica' => true, 'persona_moral' => false],
            ['clave' => 'D03', 'descripcion' => 'Gastos funerales.', 'persona_fisica' => true, 'persona_moral' => false],
            ['clave' => 'D04', 'descripcion' => 'Donativos', 'persona_fisica' => true, 'persona_moral' => false],
            ['clave' => 'D05', 'descripcion' => 'Intereses reales efectivamente pagados por créditos hipotecarios', 'persona_fisica' => true, 'persona_moral' => false],
            ['clave' => 'D06', 'descripcion' => 'Aportaciones voluntarias al SAR', 'persona_fisica' => true, 'persona_moral' => false],
            ['clave' => 'D07', 'descripcion' => 'Primas por seguros de gastos médicos', 'persona_fisica' => true, 'persona_moral' => false],
            ['clave' => 'D08', 'descripcion' => 'Gastos de transportación escolar obligatoria', 'persona_fisica' => true, 'persona_moral' => false],
            ['clave' => 'D09', 'descripcion' => 'Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones.', 'persona_fisica' => true, 'persona_moral' => false],
            ['clave' => 'D10', 'descripcion' => 'Pagos por servicios educativos (colegiaturas)', 'persona_fisica' => true, 'persona_moral' => false],
            ['clave' => 'S01', 'descripcion' => 'Sin efectos fiscales', 'persona_fisica' => true, 'persona_moral' => true],
            ['clave' => 'CP01', 'descripcion' => 'Pagos', 'persona_fisica' => true, 'persona_moral' => true],
            ['clave' => 'CN01', 'descripcion' => 'Nómina', 'persona_fisica' => true, 'persona_moral' => true],
        ]);

        DB::table('regimenes_fiscales')->insert([

            ['clave' => '601', 'descripcion' => 'General de Ley Personas Morales', 'persona_fisica' => false, 'persona_moral' => true],
            ['clave' => '603', 'descripcion' => 'Personas Morales con Fines no Lucrativos', 'persona_fisica' => false, 'persona_moral' => true],
            ['clave' => '605', 'descripcion' => 'Sueldos y Salarios e Ingresos Asimilados a Salarios', 'persona_fisica' => true, 'persona_moral' => false],
            ['clave' => '606', 'descripcion' => 'Arrendamiento', 'persona_fisica' => true, 'persona_moral' => false],
            ['clave' => '608', 'descripcion' => 'Demás ingresos', 'persona_fisica' => true, 'persona_moral' => false],
            ['clave' => '609', 'descripcion' => 'Consolidación', 'persona_fisica' => false, 'persona_moral' => true],
            ['clave' => '610', 'descripcion' => 'Residentes en el extranjero sin establecimiento permanente en México', 'persona_fisica' => true, 'persona_moral' => true],
            ['clave' => '611', 'descripcion' => 'Ingresos por Dividendos (socios y accionistas)', 'persona_fisica' => true, 'persona_moral' => false],
            ['clave' => '612', 'descripcion' => 'Personas Físicas con Actividades Empresariales y Profesionales', 'persona_fisica' => true, 'persona_moral' => false],
            ['clave' => '614', 'descripcion' => 'Ingresos por intereses', 'persona_fisica' => true, 'persona_moral' => false],
            ['clave' => '615', 'descripcion' => 'Régimen de los ingresos por obtención de premios', 'persona_fisica' => true, 'persona_moral' => false],
            ['clave' => '616', 'descripcion' => 'Sin obligaciones fiscales', 'persona_fisica' => true, 'persona_moral' => true],
            ['clave' => '620', 'descripcion' => 'Sociedades Cooperativas de Producción que optan por diferir sus ingresos', 'persona_fisica' => false, 'persona_moral' => true],
            ['clave' => '621', 'descripcion' => 'Incorporación Fiscal', 'persona_fisica' => true, 'persona_moral' => false],
            ['clave' => '622', 'descripcion' => 'Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras', 'persona_fisica' => false, 'persona_moral' => true],
            ['clave' => '623', 'descripcion' => 'Opcional para Grupos de Sociedades', 'persona_fisica' => false, 'persona_moral' => true],
            ['clave' => '624', 'descripcion' => 'Coordinados', 'persona_fisica' => false, 'persona_moral' => true],
            ['clave' => '625', 'descripcion' => 'Régimen de las Actividades Empresariales con ingresos a través de Plataformas Tecnológicas', 'persona_fisica' => true, 'persona_moral' => false],
            ['clave' => '626', 'descripcion' => 'Régimen Simplificado de Confianza', 'persona_fisica' => true, 'persona_moral' => true],
        ]);

        DB::table('objetos_impuesto')->insert([
            ['clave' => '01', 'descripcion' => 'No objeto de impuesto'],
            ['clave' => '02', 'descripcion' => 'Sí objeto de impuesto'],
            ['clave' => '03', 'descripcion' => 'Sí objeto de impuesto y no obligado al desglose'],
        ]);

        DB::table('claves_prod_serv')->insert([
            ['clave' => '01010101', 'descripcion' => 'No existe en el catálogo', 'activo' => true],
            ['clave' => '10101504', 'descripcion' => 'Frutas frescas', 'activo' => true],
            ['clave' => '10101700', 'descripcion' => 'Verduras', 'activo' => true],
            ['clave' => '14111506', 'descripcion' => 'Agua embotellada', 'activo' => true],
            ['clave' => '43211503', 'descripcion' => 'Computadoras portátiles', 'activo' => true],
            ['clave' => '43212105', 'descripcion' => 'Impresoras', 'activo' => true],
        ]);

        DB::table('claves_unidad')->insert([
            ['clave' => 'H87', 'nombre' => 'Pieza', 'activo' => true],
            ['clave' => 'E48', 'nombre' => 'Unidad de servicio', 'activo' => true],
            ['clave' => 'KGM', 'nombre' => 'Kilogramo', 'activo' => true],
            ['clave' => 'LTR', 'nombre' => 'Litro', 'activo' => true],
            ['clave' => 'MTR', 'nombre' => 'Metro', 'activo' => true],
            ['clave' => 'MLT', 'nombre' => 'Mililitro', 'activo' => true],
            ['clave' => 'SET', 'nombre' => 'Juego', 'activo' => true],
        ]);
    }
}
