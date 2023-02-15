<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" 
         xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" 
         xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" 
         xmlns:ccts="urn:un:unece:uncefact:documentation:2" 
         xmlns:ds="http://www.w3.org/2000/09/xmldsig#" 
         xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" 
         xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" 
         xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1" 
         xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2">
<ext:UBLExtensions>
  <ext:UBLExtension>
    <ext:ExtensionContent>
      <sac:AdditionalInformation>
        <sac:AdditionalMonetaryTotal>
          <cbc:ID>${codigoMontoDescuentosSwf}</cbc:ID>
          <cbc:PayableAmount currencyID="${moneda}">${totalDescuento}</cbc:PayableAmount>
        </sac:AdditionalMonetaryTotal>
        <sac:AdditionalMonetaryTotal>  
          <cbc:ID>${codigoMontoOperGravadasSwf}</cbc:ID>
          <cbc:PayableAmount currencyID="${moneda}">${montoOperGravadas}</cbc:PayableAmount>
        </sac:AdditionalMonetaryTotal>  
        <sac:AdditionalMonetaryTotal>  
          <cbc:ID>${codigoMontoOperInafectasSwf}</cbc:ID>
          <cbc:PayableAmount currencyID="${moneda}">${montoOperInafectas}</cbc:PayableAmount>
        </sac:AdditionalMonetaryTotal>  
        <sac:AdditionalMonetaryTotal>
          <cbc:ID>${codigoMontoOperExoneradasSwf}</cbc:ID>
          <cbc:PayableAmount currencyID="${moneda}">${montoOperExoneradas}</cbc:PayableAmount>
        </sac:AdditionalMonetaryTotal>
        <#if baseImponiblePercepcion?? && montoPercepcion?? && montoTotalSumPercepcion?? && montoTotalSumPercepcion??> 
        <sac:AdditionalMonetaryTotal>
          <cbc:ID schemeID="${codRegiPercepcion}">${codigoPercepSwf}</cbc:ID>
          <sac:ReferenceAmount currencyID="${codigoMonedaSolesSwf}">${baseImponiblePercepcion}</sac:ReferenceAmount>
          <cbc:PayableAmount currencyID="${codigoMonedaSolesSwf}">${montoPercepcion}</cbc:PayableAmount>
          <sac:TotalAmount currencyID="${codigoMonedaSolesSwf}">${montoTotalSumPercepcion}</sac:TotalAmount>
        </sac:AdditionalMonetaryTotal>
        </#if>
        <#if totalVentaOperGratuita??>
        <sac:AdditionalMonetaryTotal>  
          <cbc:ID>${codigoGratuitoSwf}</cbc:ID>
          <cbc:PayableAmount currencyID="${moneda}">${totalVentaOperGratuita}</cbc:PayableAmount>
        </sac:AdditionalMonetaryTotal>   
        </#if>
        <#list listaLeyendas as leyenda>
        <sac:AdditionalProperty>  
          <cbc:ID>${leyenda.codigo}</cbc:ID>
          <cbc:Value>${leyenda.descripcion}</cbc:Value>
        </sac:AdditionalProperty>    
        </#list>
        <sac:SUNATTransaction>
          <cbc:ID>${tipoOperacion}</cbc:ID> 
        </sac:SUNATTransaction>
        </sac:AdditionalInformation>
    </ext:ExtensionContent>
  </ext:UBLExtension>
  <ext:UBLExtension>
    <ext:ExtensionContent>
    </ext:ExtensionContent>
  </ext:UBLExtension>
</ext:UBLExtensions> 
<cbc:UBLVersionID>${ublVersionIdSwf}</cbc:UBLVersionID>
<cbc:CustomizationID>${CustomizationIdSwf}</cbc:CustomizationID>
<cbc:ID>${nroCdpSwf}</cbc:ID>
<cbc:IssueDate>${fechaEmision}</cbc:IssueDate>
<cbc:InvoiceTypeCode>${tipCdpSwf}</cbc:InvoiceTypeCode>
<cbc:DocumentCurrencyCode>${moneda}</cbc:DocumentCurrencyCode>
<#list listaRelacionado as relacion>
  <#if relacion.indDocuRelacionado = "3">  
    <cac:OrderReference>
      <cbc:ID>${relacion.nroDocuRelacionado}</cbc:ID>
    </cac:OrderReference>
  </#if>
</#list>
<#list listaRelacionado as relacion>
<#if relacion.indDocuRelacionado = "1">
  <cac:DespatchDocumentReference>
    <cbc:ID>${relacion.nroDocuRelacionado}</cbc:ID>
    <cbc:DocumentTypeCode>${relacion.tipDocuRelacionado}</cbc:DocumentTypeCode>
  </cac:DespatchDocumentReference>
</#if>
</#list>
<#list listaRelacionado as relacion>
  <#if relacion.indDocuRelacionado = "99">
    <cac:AdditionalDocumentReference>
      <cbc:ID>${relacion.nroDocuRelacionado}</cbc:ID>
      <cbc:DocumentTypeCode>${relacion.tipDocuRelacionado}</cbc:DocumentTypeCode>
    </cac:AdditionalDocumentReference>
  </#if>
</#list>
<cac:Signature>
  <cbc:ID>${nroRucEmisorSwf}</cbc:ID>
  <cbc:Note>${identificadorFacturadorSwf}</cbc:Note>
  <cbc:ValidatorID>${codigoFacturadorSwf}</cbc:ValidatorID>
  <cac:SignatoryParty>
      <cac:PartyIdentification>
        <cbc:ID>${nroRucEmisorSwf}</cbc:ID>
      </cac:PartyIdentification>
      <cac:PartyName>
        <cbc:Name>${nombreComercialSwf}</cbc:Name>
      </cac:PartyName>
      <cac:AgentParty>
        <cac:PartyIdentification>
          <cbc:ID>${nroRucEmisorSwf}</cbc:ID>
        </cac:PartyIdentification>
        <cac:PartyName>
          <cbc:Name>${razonSocialSwf}</cbc:Name>
        </cac:PartyName>
        <cac:PartyLegalEntity>
          <cbc:RegistrationName>${razonSocialSwf}</cbc:RegistrationName>
        </cac:PartyLegalEntity>
      </cac:AgentParty>
    </cac:SignatoryParty>
    <cac:DigitalSignatureAttachment>
      <cac:ExternalReference>
        <cbc:URI>${identificadorFirmaSwf}</cbc:URI>
      </cac:ExternalReference>
    </cac:DigitalSignatureAttachment>
</cac:Signature>
<cac:AccountingSupplierParty>
  <cbc:CustomerAssignedAccountID>${nroRucEmisorSwf}</cbc:CustomerAssignedAccountID>
  <cbc:AdditionalAccountID>${tipDocuEmisorSwf}</cbc:AdditionalAccountID>
  <cac:Party>
    <cac:PartyName>
      <cbc:Name>${nombreComercialSwf}</cbc:Name>
    </cac:PartyName>
    <cac:PostalAddress>
      <cbc:ID>${ubigeoDomFiscalSwf}</cbc:ID>
      <cbc:StreetName>${direccionDomFiscalSwf}</cbc:StreetName>
      <cac:Country>
        <cbc:IdentificationCode>${paisDomFiscalSwf}</cbc:IdentificationCode>
      </cac:Country>
    </cac:PostalAddress>
    <cac:PartyLegalEntity>
      <cbc:RegistrationName>${razonSocialSwf}</cbc:RegistrationName>
    </cac:PartyLegalEntity>
  </cac:Party>
</cac:AccountingSupplierParty>
<cac:AccountingCustomerParty>
  <cbc:CustomerAssignedAccountID>${nroDocumento}</cbc:CustomerAssignedAccountID>
  <cbc:AdditionalAccountID>${tipoDocumento}</cbc:AdditionalAccountID>
  <cac:Party>
    <#if codigoPaisCliente?? && codigoUbigeoCliente?? && direccionCliente?? && direccionCliente??>
    <cac:PostalAddress>
      <cbc:ID>${codigoUbigeoCliente}</cbc:ID>
      <cbc:StreetName>${direccionCliente}</cbc:StreetName>
      <cac:Country>
        <cbc:IdentificationCode>${codigoPaisCliente}</cbc:IdentificationCode>
      </cac:Country>
    </cac:PostalAddress>
    <cac:PhysicalLocation>
      <cbc:Description>${direccionCliente}</cbc:Description>
    </cac:PhysicalLocation>
    </#if>
    <cac:PartyLegalEntity>
      <cbc:RegistrationName>${razonSocialUsuario}</cbc:RegistrationName>
    </cac:PartyLegalEntity>
  </cac:Party>
</cac:AccountingCustomerParty>
<cac:SellerSupplierParty>
  <cac:Party>
    <cac:PostalAddress>
      <#if codigoUbigeoEntrega??>
      <cbc:ID>${codigoUbigeoEntrega}</cbc:ID>
      </#if>
      <cbc:AddressTypeCode>${direccionUsuario}</cbc:AddressTypeCode>
      <#if direccionCompletaEntrega?? && direccionCompletaEntrega??>
      <cbc:StreetName>${direccionCompletaEntrega}</cbc:StreetName>
      <cac:Country>
        <cbc:IdentificationCode>${codigoPaisEntrega}</cbc:IdentificationCode>
      </cac:Country>
      </#if>
    </cac:PostalAddress>
  </cac:Party>
</cac:SellerSupplierParty>
<#if fechaVencimiento??>
<cac:PaymentMeans>
  <cbc:PaymentMeansCode>-</cbc:PaymentMeansCode>
  <cbc:PaymentDueDate>${fechaVencimiento}</cbc:PaymentDueDate>
</cac:PaymentMeans>
</#if>
<#if totalAnticipos??>
<#list listaRelacionado as relacion>
  <#if relacion.indDocuRelacionado = "2">
    <cac:PrepaidPayment>
      <cbc:ID>${relacion.nroDocuRelacionado}</cbc:ID>
      <cbc:PaidAmount currencyID="${moneda}">${totalAnticipos}</cbc:PaidAmount>
      <cbc:InstructionID>${relacion.nroDocuEmisor}</cbc:InstructionID>
    </cac:PrepaidPayment>
  </#if>  
</#list>
</#if>
<#list listaRelacionado as relacion>
  <#if relacion.indDocuRelacionado = "1">
    <cac:PrepaidPayment>
      <cbc:ID>${relacion.nroDocuRelacionado}</cbc:ID>
      <cbc:PaidAmount currencyID="${moneda}">${relacion.mtoDocuRelacionado}</cbc:PaidAmount>
      <cbc:InstructionID>${relacion.nroDocuEmisor}</cbc:InstructionID>    
    </cac:PrepaidPayment>
  </#if>
</#list>
<#list listaRelacionado as relacion>
  <#if relacion.indDocuRelacionado = "3">  
    <cac:PrepaidPayment>
      <cbc:ID>${relacion.nroDocuRelacionado}</cbc:ID>
      <cbc:PaidAmount currencyID="${moneda}">${relacion.mtoDocuRelacionado}</cbc:PaidAmount>
      <cbc:InstructionID>${relacion.nroDocuEmisor}</cbc:InstructionID>    
    </cac:PrepaidPayment>
  </#if>
</#list>
<#assign sumatoriaIsc = sumaIsc?number>
<#if (sumatoriaIsc > 0)>
<cac:TaxTotal>  
  <cbc:TaxAmount currencyID="${moneda}">${sumaIsc}</cbc:TaxAmount>
  <cac:TaxSubtotal>
    <cbc:TaxAmount currencyID="${moneda}">${sumaIsc}</cbc:TaxAmount>
    <cac:TaxCategory>
      <cac:TaxScheme>
        <cbc:ID>${idIsc}</cbc:ID>
        <cbc:Name>${codIsc}</cbc:Name>
        <cbc:TaxTypeCode>${codExtIsc}</cbc:TaxTypeCode>
      </cac:TaxScheme>
    </cac:TaxCategory>  
  </cac:TaxSubtotal>
</cac:TaxTotal>
</#if>
<#assign sumatoriaIgv = sumaIgv?number>
<#if (sumatoriaIgv > 0)>
<cac:TaxTotal>
  <cbc:TaxAmount currencyID="${moneda}">${sumaIgv}</cbc:TaxAmount>
  <cac:TaxSubtotal>
    <cbc:TaxAmount currencyID="${moneda}">${sumaIgv}</cbc:TaxAmount>
    <cac:TaxCategory>
      <cac:TaxScheme>
        <cbc:ID>${idIgv}</cbc:ID>
        <cbc:Name>${codIgv}</cbc:Name>
        <cbc:TaxTypeCode>${codExtIgv}</cbc:TaxTypeCode>
      </cac:TaxScheme>
    </cac:TaxCategory>  
  </cac:TaxSubtotal>
</cac:TaxTotal>
</#if>
<#assign sumatoriaOtros = sumaOtros?number>
<#if (sumatoriaOtros > 0)>
<cac:TaxTotal>  
  <cbc:TaxAmount currencyID="${moneda}">${sumaOtros}</cbc:TaxAmount>
  <cac:TaxSubtotal>
    <cbc:TaxAmount currencyID="${moneda}">${sumaOtros}</cbc:TaxAmount>
    <cac:TaxCategory>
      <cac:TaxScheme>
        <cbc:ID>${idOtr}</cbc:ID>
        <cbc:Name>${codOtr}</cbc:Name>
        <cbc:TaxTypeCode>${codExtOtr}</cbc:TaxTypeCode>
      </cac:TaxScheme>
    </cac:TaxCategory>  
  </cac:TaxSubtotal>
</cac:TaxTotal>
</#if>
<cac:LegalMonetaryTotal>
  <cbc:AllowanceTotalAmount currencyID="${moneda}">${descuentoGlobal}</cbc:AllowanceTotalAmount>
  <cbc:ChargeTotalAmount currencyID="${moneda}">${sumaOtrosCargos}</cbc:ChargeTotalAmount>
  <cbc:PayableAmount currencyID="${moneda}">${sumaImporteVenta}</cbc:PayableAmount>
</cac:LegalMonetaryTotal>
<#list listaDetalle as detalle>
<cac:InvoiceLine>
<cbc:ID>${detalle.lineaSwf}</cbc:ID>
<cbc:InvoicedQuantity unitCode="${detalle.unidadMedida}">${detalle.cantItem}</cbc:InvoicedQuantity>
<cbc:LineExtensionAmount currencyID="${moneda}">${detalle.valorVentaItem}</cbc:LineExtensionAmount>
<#assign pvUnitarioItem = detalle.precioVentaUnitarioItem?number>

<cac:PricingReference>
  <cac:AlternativeConditionPrice>
    <cbc:PriceAmount currencyID="${moneda}">${detalle.precioVentaUnitarioItem}</cbc:PriceAmount>
    <cbc:PriceTypeCode>${tipoCodigoMonedaSwf}</cbc:PriceTypeCode>
  </cac:AlternativeConditionPrice>
<#if detalle.monto??> 
<#assign valorReferencial = detalle.monto?number>
<#if (valorReferencial > 0) >
  <cac:AlternativeConditionPrice>
      <cbc:PriceAmount currencyID="${moneda}">${detalle.monto}</cbc:PriceAmount>
      <cbc:PriceTypeCode>${detalle.tipoCodiMoneGratiSwf}</cbc:PriceTypeCode>
  </cac:AlternativeConditionPrice>
</#if>
</#if>
</cac:PricingReference> 
<cac:AllowanceCharge>
  <cbc:ChargeIndicator>false</cbc:ChargeIndicator>
  <cbc:Amount currencyID="${moneda}">${detalle.descuentoItem}</cbc:Amount>
</cac:AllowanceCharge>
<#assign sumaIscLinea = detalle.montoIscItem?number>
<#if (sumaIscLinea > 0)>
<cac:TaxTotal>  
  <cbc:TaxAmount currencyID="${moneda}">${detalle.montoIscItem}</cbc:TaxAmount>
  <cac:TaxSubtotal>
    <cbc:TaxableAmount currencyID="${moneda}">${detalle.montoIscItem}</cbc:TaxableAmount>
    <cbc:TaxAmount currencyID="${moneda}">${detalle.montoIscItem}</cbc:TaxAmount>
    <cac:TaxCategory>
      <cbc:TierRange>${detalle.tipoSistemaIsc}</cbc:TierRange>
      <cac:TaxScheme>
        <cbc:ID>${idIsc}</cbc:ID>
        <cbc:Name>${codIsc}</cbc:Name>
        <cbc:TaxTypeCode>${codExtIsc}</cbc:TaxTypeCode>
      </cac:TaxScheme>
    </cac:TaxCategory>
  </cac:TaxSubtotal>
</cac:TaxTotal>
</#if>
<#assign sumaIgvLinea = detalle.montoIgvItem?number>
<#if (sumaIgvLinea > 0) || ((sumaIgvLinea == 0)&&(detalle.afectaIgvItem != "10")) >
<cac:TaxTotal>
  <cbc:TaxAmount currencyID="${moneda}">${detalle.montoIgvItem}</cbc:TaxAmount>
  <cac:TaxSubtotal>
    <cbc:TaxableAmount currencyID="${moneda}">${detalle.montoIgvItem}</cbc:TaxableAmount>
    <cbc:TaxAmount currencyID="${moneda}">${detalle.montoIgvItem}</cbc:TaxAmount>
    <cac:TaxCategory>
      <cbc:TaxExemptionReasonCode>${detalle.afectaIgvItem}</cbc:TaxExemptionReasonCode>
      <cac:TaxScheme>
        <cbc:ID>${idIgv}</cbc:ID>
        <cbc:Name>${codIgv}</cbc:Name>
        <cbc:TaxTypeCode>${codExtIgv}</cbc:TaxTypeCode>
      </cac:TaxScheme>  
    </cac:TaxCategory>
  </cac:TaxSubtotal>
</cac:TaxTotal>
</#if>
<cac:Item>
  <cbc:Description>${detalle.desItem}</cbc:Description>
  <cac:SellersItemIdentification>
      <cbc:ID>${detalle.codiProducto}</cbc:ID>
  </cac:SellersItemIdentification>
  <cac:AdditionalItemIdentification>
      <cbc:ID>${detalle.codiSunat}</cbc:ID>
  </cac:AdditionalItemIdentification>
  <#if detalle.placa??>
  <cac:AdditionalItemProperty>
    <cbc:Name>${detalle.tipoCodigoPlacaSwf}</cbc:Name>
    <cbc:Value>${detalle.placa}</cbc:Value>
  </cac:AdditionalItemProperty>
  </#if>
</cac:Item>
<cac:Price>
  <cbc:PriceAmount currencyID="${moneda}">${detalle.valorUnitario}</cbc:PriceAmount>
</cac:Price>
</cac:InvoiceLine>
</#list>
</Invoice>